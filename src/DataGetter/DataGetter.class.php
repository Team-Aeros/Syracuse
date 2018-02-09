<?php
namespace Syracuse\src\DataGetter;
use Syracuse\src\headers\Controller;
use Syracuse\src\database\Database;
/*
 * lijn 128/161 datum goed
 * lijn 169 pasthour
 */
/**
 * Class DataGetter
 * @package Syracuse\src\DataGetter
 */
class DataGetter extends Controller {
    private $currentDate;
    private $path;
    private $valid_caribbeanStations;
    private $valid_gulfStations;

    /**
     * DataGetter constructor.
     * sets the currentDate
     * sets the path to the webdav folder
     * sets the valid stations arrays
     */
    public function __construct() {
        date_default_timezone_set('Europe/Amsterdam');
        $this->currentDate = date('Y-m-d', time());
        $this->loadSettings();
        $this->path = self::$config->get('path') . '/../webdav';
        #for rain data

        $this->valid_caribbeanStations =["765905", "765906", "766491", "766493", "782550", "782623", "782640",
            "783460", "783550", "783670", "783830", "783840", "783880", "783970", "784390", "784570",
            "784580", "784600", "784785", "784790", "784840", "784850", "784860", "785140", "785145",
            "785201", "785203", "785260", "785263", "785350", "785430", "785470", "787030", "787050",
            "787080", "787110", "787300", "787450", "788580", "788610", "788620", "788660", "788970",
            "789050", "789060", "789250", "789510", "789580", "789820", "789880", "789900", "800010",
            "804020", "804030", "804050"];
        #for temperature data

        $this->valid_gulfStations = ["720273", "722010", "722014", "722015", "722016", "722034", "722038",
            "722041", "722064", "722104", "722106", "722108", "722110", "722115", "722116", "722159",
            "722200", "722209", "722320", "722351", "722406", "722408", "722420", "722422", "722423",
            "722427", "722435", "722436", "722500", "722505", "722510", "722515", "722516", "722524",
            "722527", "722543", "722555", "722682", "747880", "763993", "765480", "765491", "765493",
            "765494", "765905", "765906", "766127", "766440", "766443", "766491", "766493", "766870",
            "766910", "766913", "766920", "766950", "782240", "782290", "783250", "783284"];

    }

    /**
     * Uses findDataLinksTemp() to get the paths to the valid json files and reads them to make an array with all the data
     * @return array, containing al the temperature data values as Key(the station number) => Value(array containing all the temperature values from the last hour of that station)
     */
    public function getTempDataFiles() {
        $stationTempDataLinks = $this->findDataLinksTemp();
        $dataFiles = [];
        foreach ($stationTempDataLinks as $station) {
            /*echo "<pre>";
            var_dump($station);
            echo "</pre>";*/
            $stationFiles = [];
            foreach ($station as $link) {
                $file = file_get_contents($link);
                $json = json_decode($file, true);

                foreach ($json as $decodedJsonFile) {
                    $file = $decodedJsonFile['temperature'];
                    if(key_exists($decodedJsonFile['station'], $stationFiles)) {
                        $stationFiles[$decodedJsonFile['station']][] = $file;
                    } else {
                        $stationFiles[$decodedJsonFile['station']] = [$file];
                    }
                }
            }
            $dataFiles[] = $stationFiles;
        }
        return $dataFiles;
    }
    /**
     * Uses findDataLinksRain() to get the paths to the valid json files and reads them to make an array with all the data
     * @return array, containing all the rain data values as key(station number) => value(array of data(station,precipitation,temperature,windspeed) of that station)
     */
    public function getRainDataFiles() {
        $stationRainDataLinks = $this->findDataLinksRain();
        $dataFiles = [];
        foreach ($stationRainDataLinks as $station) {
            $highTemp = null;
            $lowTemp = null;
            for($i=0; $i<count($station); $i++) {
                $file = file_get_contents($station[$i]);
                $json = json_decode($file, true);
                if($highTemp != null) {
                    if ($json[$i]['temperature'] > $highTemp) {
                        $highTemp = $json[$i]['temperature'];
                    }
                } else {
                    $highTemp = $json[$i]['temperature'];
                }
                if ($lowTemp != null) {
                    if ($json[$i]['temperature'] < $lowTemp){
                        $lowTemp = $json[$i]['temperature'];
                    }
                } else {
                    $lowTemp = $json[$i]['temperature'];
                }
                $endFile['htemp'] =  $highTemp;
                $endFile['ltemp'] = $lowTemp;
            }
            $mostRecentFile = file_get_contents($station[count($station)-1]);
            $json2 = json_decode($mostRecentFile, true);

            $decodedJsonFile = $json2[count($json2) - 1] ?? $json2[0];
            $endFile = ["name" => $this->getStationName($decodedJsonFile['station']), "station" => $decodedJsonFile['station'], "precipitation" => $decodedJsonFile['precipitation'], "High_temperature" => $highTemp, "Low_temperature" => $highTemp, "wind_speed" => $decodedJsonFile['wind_speed']];
            $dataFiles[] = $endFile;
        }

        usort($dataFiles, function ($a, $b) {
            $result = 0;
            if ($b['precipitation'] > $a['precipitation']) {
                $result = 1;
            } elseif ($b['precipitation'] < $a['precipitation']) {
                $result = -1;
            }
            return $result;
        });
        $top10 = array_slice($dataFiles,0,10);
        return $top10;
    }

    /**
     * @param $stationID, the stationID that needs a corresponding name
     * Queries the database for the station name corresponding with the station ID given as parameter.
     * @return string, the database name
     */
    private function getStationName($stationID) {
        $result = Database::interact('retrieve', 'station')
            ->fields('name')
            ->where(['stn', $stationID])
            ->getSingle();
        return ucwords(strtolower($result['name'] ?? _translate('unknown_station')));
    }

    /**
     * @param $valid_caribbeanStations, all the valid stationID's in the caribbean sea.
     * finds all the paths to valid jsons in the webdav folder for the rain data
     * @return array, containing all the paths to valid jsons as Key(stationID) => Value(array of paths for that station)
     */
    private function findDataLinksRain() {
        $dataLinks = [];
        #get the paths to the valid stations
        foreach (scandir($this->path) as $station) {
            if (in_array($station, $this->valid_caribbeanStations)) { #ONLY CHECKS CARIB STATIONS BECAUSE ONLY READ RAIN THERE
                if (is_dir($this->path . '/' . $station) && !in_array($station, ['index.php', '.', '..'])) {
                    $link = $this->path . '/' . $station;
                    foreach (scandir($link) as $dateInLink) {
                        if (!in_array($dateInLink, ['index.php', '.', '..']) && $dateInLink == trim($this->currentDate)) {
                            $link = $link . "/" . $dateInLink;
                            foreach (scandir($link) as $fileInFolder) {
                                if (is_file($link . "/" . $fileInFolder) && !in_array($fileInFolder, ['index.php', '.', '..'])) {
                                    if (key_exists($station,$dataLinks)) {
                                        $dataLinks[$station][] = $link."/".$fileInFolder;
                                    }else {
                                        $dataLinks[$station] =  [$link . "/" . $fileInFolder];
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }
        return $dataLinks;
    }

    /**
     * @param $valid_gulfStations, all the valid stationID's in the Gulf of Mexico.
     * finds all the paths to valid jsons in the webdav folder for the temperature data
     * @return array, containing all the paths to valid jsons as Key(stationID) => Value(array of paths for that station)
     */
    private function findDataLinksTemp() {
        $dataLinks = [];
        foreach (scandir($this->path) as $station) {
            if (in_array($station, $this->valid_gulfStations)) { #ONLY CHECKS GULF STATIONS BECAUSE ONLY READ TEMP THERE
                if (is_dir($this->path . '/' . $station) && !in_array($station, ['index.php', '.', '..'])) {
                    $link = $this->path . '/' . $station;
                    foreach (scandir($link) as $dateInLink) {
                        if (!in_array($dateInLink, ['index.php', '.', '..']) && $dateInLink == trim($this->currentDate)) {
                            $link = $link . "/" . $dateInLink;
                            foreach (scandir($link) as $fileInFolder) {
                                if (is_file($link . "/" . $fileInFolder) && !in_array($fileInFolder, ['index.php', '.', '..'])) {
                                    $arrayTime = explode("-",date("H-i", time()));

                                    $currentTimeVals = [];
                                    foreach ($arrayTime as $val) {
                                        $currentTimeVals[] = (int) $val ;
                                    }


                                    $fileArrayTime = explode("_",$fileInFolder);
                                    $fileTimeVals = [];
                                    foreach ($fileArrayTime as $val) {
                                        $fileTimeVals[] = (int) $val;
                                    }
                                    $maxHour = $fileTimeVals[0] + 1 ;
                                    $fileHour = $fileTimeVals[0] + 1;
                                    $fileMinute = $fileTimeVals[1];
                                    /*echo "FILE HOUR: " . $fileHour;
                                    echo "<br>";
                                    echo "PAST HOUR: " . $maxHour;
                                    echo "<br>";
                                    echo "FILE MIN: " . $fileMinute;
                                    echo "<br>";
                                    echo "CUR MIN: " . $currentTimeVals[1];
                                    echo "<br>";*/

                                    if($fileHour >= $currentTimeVals[0]) {
                                        if($fileMinute <= $currentTimeVals[1]) {
                                            if (key_exists($station, $dataLinks)) {
                                                $dataLinks[$station][] = $link . "/" . $fileInFolder;
                                            } else {
                                                $dataLinks[$station] = [$link . "/" . $fileInFolder];
                                            }
                                        }
                                    } elseif ($fileHour <= $maxHour) {
                                        if($fileMinute >= $currentTimeVals[1]) {
                                            if (key_exists($station, $dataLinks)) {
                                                $dataLinks[$station][] = $link . "/" . $fileInFolder;
                                            } else {
                                                $dataLinks[$station] = [$link . "/" . $fileInFolder];
                                            }

                                        }
                                    }
                                    /*else
                                        echo sprintf('%s = %s<br />', $fileTimeVals[0], $pastHour);*/
                                }
                            }
                        }
                    }
                }
            }
        }
        /*echo "<pre>";
        var_dump($dataLinks);
        echo "</pre>";*/
        return $dataLinks;
    }

    public function getStations() {
        $returnStat = [];
        $stations = Database::interact('retrieve', 'station')
            ->fields('stn', 'name', 'latitude', 'longitude')
            ->getAll();

        $temperatures = [];
        $this->loadMostRecentTemperatures($temperatures);

        foreach ($stations as $station) {
            if(in_array($station['stn'],$this->valid_gulfStations)) {
                $returnStat[] = ['stn' => $station['stn'], 'name' => ucwords(strtolower($station['name'])), 'temp' => $temperatures[$station['stn']] ?? '?', 'lat' => $station['latitude'], 'lng' => $station['longitude']];
            }
        }
        return $returnStat;
    }

    private function loadMostRecentTemperatures(array &$temperatures) : void {
        $stations = array_merge($this->valid_caribbeanStations, $this->valid_gulfStations);
        foreach ($stations as $directory) {
            if (!is_dir($this->path . '/' . $directory) || in_array($directory, ['.', '..', 'index.php']))
                continue;

            $subDirectories = scandir($this->path . '/' . $directory);
            $mostRecent = [0, 0, 0, 0, 0];

            // add comment here
            foreach ($subDirectories as $subDirectory) {
                if (!is_dir($this->path . '/' . $directory . '/' . $subDirectory) || in_array($subDirectory, ['.', '..', '.index.php']))
                    continue;

                foreach (scandir($this->path . '/' . $directory . '/' . $subDirectory) as $tempFile) {
                    if (in_array($tempFile, ['.', '..', '.index.php']))
                        continue;

                    // Yep, there are better ways to do this, I know
                    $parsedDate = array_merge(explode('-', $subDirectory), explode('_', str_replace('.json', '', $tempFile)));

                    if (count($parsedDate) != 5)
                        continue;

                    $everythingNewer = true;
                    for ($i = 0; $i < 5; $i++) {
                        if ($parsedDate[$i] < $mostRecent[$i]) {
                            $everythingNewer = false;
                            break;
                        }
                    }

                    if ($everythingNewer)
                        $mostRecent = $parsedDate;
                }
            }

            $fileToLoad = sprintf('%s/%s/%02d-%02d-%02d/%02d_%02d.json', $this->path, $directory, ...$mostRecent);
            $file = @fopen($fileToLoad, 'r');

            if (!$file)
                return;

            $json = json_decode(fread($file, filesize($fileToLoad)));
            $temperatures[$directory] = $json[count($json) - 1]->temperature;
            fclose($file);
        }
    }
}