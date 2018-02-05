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
            #var_dump($currentDateVals);

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

                echo "<pre>";
                var_dump($json);
                echo "</pre>";


                if (empty($json)) {
                    header('Location: ' . self::$config->get('url'));
                    exit;
                } else {
                    $tmpFile = ["station" => $json['station'], "date" => $json['date'], "time" => $json['time'], "temperature" => $json['temperature'], "wind_speed" => $json['wind_speed'], "precipitation" => $json["precipitation"]];
                    $this->jsonFiles[] = $tmpFile;
                }
            }
        }

    }
    public function download() {
        $downloadJson = json_encode($this->jsonFiles);
        $filename = date("d-M-Y", $this->maxLastDay) . "-" . date("d-M-Y", $this->currentDay);
        header("Content-type: application/json");
        header("Content-disposition: attachment; filename=$filename.json");
        echo $downloadJson;
    }
    public function getCurrentDate($timezone) {
        date_default_timezone_set($timezone);
        return date('Y/m/d ', time());;
    }

    /*[0] is month, [1] is day, [2] is year*/
    private function dateVals($dateString, $del) {
        $dateVals = explode($del, $dateString);
        $returnArray = [];
        foreach ($dateVals as $val) {
            $returnArray[] = (int)$val;
        }

        return $returnArray;
    }

    public function getDataLinks() {
        return $this->dataLinks;
    }

    public function getCaribStations() {
        return $this->valid_caribbeanStations;
    }

    public function getGulfStations() {
        return $this->valid_gulfStations;
    }
}