<?php
/*search main map for all valid station ID's
make array with all valid station ID's folders
loop through each map to find the folders that are max 6 days old
get all the json files of those folders
merge all the json files into 1 json file*/
namespace Syracuse\src\download;

use Syracuse\src\headers\Controller;

class DataReader extends Controller {
    public function __construct() {
        date_default_timezone_set('Europe/Amsterdam');
        $currentDate = date('m/d/Y ', time());

        $this->loadSettings();
        $start = self::$config->get('path') . '/../webdav/';

        $stations = [];
        $valid_stations = [ "720268", "720273", "720296", "720303", "720381", "720391", "722010", "722011", "722012", "722014", "722015", "722016",
            "722020", "722022", "722024", "722025", "722026", "722029", "722030", "722034", "722037", "722038", "722039", "722040",
            "722041", "722045", "722049", "722050", "722053", "722055", "722056", "722057", "722060", "722064", "722065", "722066",
            "722067", "722068", "722069", "722103", "722104", "722106", "722108", "722110", "722113", "722115", "722116", "722119",
            "722138", "722140", "722146", "722159", "722166", "722189", "722200", "722202", "722209", "722210", "722212", "722213",
            "722215", "722220", "722221", "722223", "722225", "722226", "722230", "722235", "722245", "722246", "722261", "722310",
            "722314", "722315", "722316", "722317", "722320", "722334", "722338", "722351", "722361", "722366", "722400", "722404",
            "722405", "722406", "722408", "722410", "722416", "722420", "722422", "722423", "722427", "722429", "722430", "722435",
            "722436", "722444", "722445", "722447", "722500", "722505", "722506", "722510", "722515", "722516", "722517", "722520",
            "722523", "722524", "722525", "722526", "722527", "722530", "722533", "722535", "722536", "722537", "722539", "722540",
            "722542", "722543", "722544", "722545", "722547", "722550", "722553", "722554", "722555", "722556", "722682", "723629",
            "723761", "747400", "747685", "747686", "747688", "747750", "747760", "747770", "747810", "747880", "747930", "747940",
            "747945", "747946", "747950", "749044", "749045", "749056", "749058", "749091", "762863", "763503", "763993", "764915",
            "764990", "765480", "765491", "765493", "765494", "765905", "765906", "766127", "766340", "766342", "766440", "766443",
            "766491", "766493", "766753", "766790", "766793", "766850", "766870", "766910", "766913", "766920", "766950", "767260",
            "767383", "767410", "767441", "767493", "767500", "782240", "782290", "782440", "783250", "783284", "783830", "783840",
            "749026", "749027", "749028", "749035", "765905", "765906", "766491", "766493", "767500", "782550", "782623", "782640",
            "783460", "783550", "783670", "783830", "783840", "783880", "783970", "784390", "784570", "784580", "784600", "784785",
            "784790", "784840", "784850", "784860", "785140", "785145", "785201", "785203", "785260", "785263", "785350", "785430",
            "785470", "785830", "786370", "787000", "787030", "787050", "787080", "787110", "787170", "787190", "787200", "787240",
            "787300", "787350", "787390", "787410", "787450", "787620", "787625", "787670", "787740", "787920", "787930", "787950",
            "788060", "788067", "788071", "788078", "788580", "788610", "788620", "788660", "788970", "789050", "789060", "789250",
            "789480", "789510", "789580", "789613", "789620", "789700", "789820", "789880", "789900", "800010", "800220", "800280",
            "804020", "804030", "804050", "804070", "804100", "804120", "804130", "804150", "804160", "804190", "804214", "804250",
            "804260", "804270", "804280", "804310", "804350", "804380", "804400", "804420", "804440", "804720"];

        $stationLinks = [];
        $dateLinks = [];
        $dataLinks = [];
        $directories = glob($start . "/*", GLOB_ONLYDIR);
        foreach ($directories as $dir) {
            $dirParts = explode("/", $dir);
            $stations[] = $dirParts[9];
        }
        /*echo "<pre>";
        var_dump($stations);
        echo "</pre>";*/

        foreach ($stations as $station) {
            if (in_array($station, $valid_stations)) {
                /*can insert an if to check if it is the selected station*/
                $stationLinks[] = $start . "/" . $station;
            }
        }
        /*echo "<pre>";
        var_dump($stationLinks);
        echo "</pre>";*/

        foreach ($stationLinks as $stL) {
            $folders = scandir($stL);
            /*echo "<pre>";
            var_dump($folders);
            echo "</pre>";*/
            #var_dump($files);

            foreach ($folders as $folder) {
                if (strlen($folder) > 2) {
                    $dateLinks[] = $stL ."/". $folder;
                }
            }
        }
        /*echo "<pre>";
        var_dump($dateLinks);
        echo "</pre>";*/

        foreach ($dateLinks as $link) {
            $date = substr($link, -10, 10);
            $fileDateVals = explode("-", $date);

            $dayOfFile = mktime(0, 0, 0, $fileDateVals[1], $fileDateVals[2], $fileDateVals[0]);
            $currentDateVals = $this->dateVals($currentDate, "/");
            /*echo "<pre>";
            var_dump($currentDateVals);
            echo "</pre>";*/
            $maxLastDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1] - 6, $currentDateVals[2]);
            $currentDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1], $currentDateVals[2]);
            /*echo date("Y-M-d", "$dayOfFile");
            echo "<br>";
            echo date("Y-M-d",$maxLastDay);
            echo "<br>";
            echo date("Y-M-d",$currentDay);
            echo "<br>";*/

            $files = scandir($link);
            foreach ($files as $file) {
                if (substr($file,-5,5) === ".json") {
                    $dataLinks[] = $link . "/" . $file;
                }
            }
        }
        /*echo "<pre>";
        var_dump($dataLinks);
        echo "</pre>";*/


        $jsonFiles = [];
        foreach ($dataLinks as $link) {
            $file = file_get_contents($link);
            $json = json_decode($file, true);

            if ($json == null) {
                header("Location: http://localhost/Syracuse/");
                exit;
            } else {
                $tmpFile = ["station" => $json['station'], "date" => $json['date'], "time" => $json['time'], "temperature" => $json['temperature'], "wind_speed" => $json['wind_speed'], "precipitation" => $json["precipitation"]];
                $jsonFiles[] = $tmpFile;
            }
        }
        $downloadJson = json_encode($jsonFiles);
        $filename = date("d-M-Y", $maxLastDay) . "-" . date("d-M-Y", $currentDay);
        header("Content-type: application/json");
        header("Content-disposition: attachment; filename=$filename.json");
        echo $downloadJson;

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
}