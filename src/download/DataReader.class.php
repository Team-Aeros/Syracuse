<?php
/*
 * DONT FORGET
 * DONT FORGET
 * DONT FORGET
 * lijn 98 header locatie aanpassen voor server
 * DONT FORGET
 * DONT FORGET
 * DONT FORGET
 */
namespace Syracuse\src\download;

use Syracuse\src\headers\Controller;

class DataReader extends Controller {
    private $dataLinks;
    private $valid_caribbeanStations;
    private $valid_gulfStations;
    private $jsonFiles;
    private $maxLastDay;
    private $currentDay;

    /**
     * DataReader constructor.
     * Very similar to DataGetter
     * sets current date
     * sets path to webdav folder
     * sets valid station arrays
     *
     * Unlike DataGetter the finding of valid jsons paths is done here in the constructor
     */
    public function __construct() {
        date_default_timezone_set('Europe/Amsterdam');
        $currentDate = date('m/d/Y ', time());

        $this->loadSettings();
        $start = self::$config->get('path') . '/../webdav';
        $this->valid_caribbeanStations =["765905", "765906", "766491", "766493", "782550", "782623", "782640",
            "783460", "783550", "783670", "783830", "783840", "783880", "783970", "784390", "784570",
            "784580", "784600", "784785", "784790", "784840", "784850", "784860", "785140", "785145",
            "785201", "785203", "785260", "785263", "785350", "785430", "785470", "787030", "787050",
            "787080", "787110", "787300", "787450", "788580", "788610", "788620", "788660", "788970",
            "789050", "789060", "789250", "789510", "789580", "789820", "789880", "789900", "800010",
            "804020", "804030", "804050"];
        $this->valid_gulfStations = ["720273", "722010", "722014", "722015", "722016", "722034", "722038",
            "722041", "722064", "722104", "722106", "722108", "722110", "722115", "722116", "722159",
            "722200", "722209", "722320", "722351", "722406", "722408", "722420", "722422", "722423",
            "722427", "722435", "722436", "722500", "722505", "722510", "722515", "722516", "722524",
            "722527", "722543", "722555", "722682", "747880", "763993", "765480", "765491", "765493",
            "765494", "765905", "765906", "766127", "766440", "766443", "766491", "766493", "766870",
            "766910", "766913", "766920", "766950", "782240", "782290", "783250", "783284"];
        $valid_stations = array_merge($this->valid_caribbeanStations, $this->valid_gulfStations);

        $stations = [];
        $stationLinks = [];
        $dateLinks = [];
        $this->dataLinks = [];

        foreach (scandir($start) as $dir) {
            if (is_dir($start . '/' . $dir) && !in_array($dir, ['index.php', '.', '..']))
                $stations[] = $dir;
        }

        foreach ($stations as $station) {
            if (in_array($station, $valid_stations)) {
                /*can insert an if to check if it is the selected station*/
                $stationLinks[] = $start . "/" . $station;
            }
        }

        foreach ($stationLinks as $stL) {
            $folders = scandir($stL);

            foreach ($folders as $folder) {
                if (strlen($folder) > 2) {
                    $dateLinks[] = $stL ."/". $folder;
                }
            }
        }

        foreach ($dateLinks as $link) {
            $date = substr($link, -10, 10);
            $fileDateVals = explode("-", $date);

            $dayOfFile = mktime(0, 0, 0, $fileDateVals[1], $fileDateVals[2], $fileDateVals[0]);
            $currentDateVals = $this->dateVals($currentDate, "/");

            $this->maxLastDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1] - 6, $currentDateVals[2]);
            $this->currentDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1], $currentDateVals[2]);

            if($dayOfFile >= $this->maxLastDay and $dayOfFile <= $this->currentDay) {
                $files = scandir($link);
                foreach ($files as $file) {
                    if (substr($file, -5, 5) === ".json") {
                        $this->dataLinks[] = $link . "/" . $file;
                    }
                }
            }
        }

        $this->jsonFiles = [];
        foreach ($this->dataLinks as $link) {

            $file = file_get_contents($link);
            if (!empty($file)) {
                $json = json_decode($file, true);

                if (empty($json)) {
                    header('Location: ' . self::$config->get('url'));
                    exit;
                } else {
                    foreach ($json as $decodedJson) {
                        $tmpFile = ["station" => $decodedJson['station'], "date" => $decodedJson['date'], "time" => $decodedJson['time'], "temperature" => $decodedJson['temperature'], "wind_speed" => $decodedJson['wind_speed'], "precipitation" => $decodedJson["precipitation"]];
                        $this->jsonFiles[] = $tmpFile;
                    }
                }
            }
        }

    }

    /**
     * Uses the jsonFiles array filled in the constructor and writes it to a csv file
     * It then gets the csv file contents and deletes the file
     * Then it offers the csv file to be downloaded with the timeframe of the data as its name
     */
    public function download() {
        $json_obj = $this->jsonFiles;
        $fp = fopen('download.csv', 'w');
        fputcsv($fp, ["Station","date","time","temperature", "wind speed", "rainfall"]);
        foreach ($json_obj as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $csv_file = file_get_contents(self::$config->get('path') . '/download.csv');
        unlink("download.csv");
        $filename = date("d-M-Y", $this->maxLastDay) . "-" . date("d-M-Y", $this->currentDay);
        header("Content-type: text/csv; charset=utf-8");
        header("Content-disposition: attachment; filename=$filename.csv");
        echo $csv_file;
    }
    public function getCurrentDate($timezone) {
        date_default_timezone_set($timezone);
        return date('Y/m/d ', time());;
    }

    /**
     * explodes a date string in array and converts it values to ints
     * [0] is month, [1] is day, [2] is year
     */
    private function dateVals($dateString, $del) {
        $dateVals = explode($del, $dateString);
        $returnArray = [];
        foreach ($dateVals as $val) {
            $returnArray[] = (int)$val;
        }

        return $returnArray;
    }
}