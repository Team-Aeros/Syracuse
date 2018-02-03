<?php
/* currently only for rain*/
class DataGetter {
    private $currentDate;
    private $path;
    public function __construct() {
        date_default_timezone_set('Europe/Amsterdam');
        $this->currentDate = date('Y-m-d ', time());
        #for rain data
        $valid_caribbeanStations =["765905", "765906", "766491", "766493", "782550", "782623", "782640",
            "783460", "783550", "783670", "783830", "783840", "783880", "783970", "784390", "784570",
            "784580", "784600", "784785", "784790", "784840", "784850", "784860", "785140", "785145",
            "785201", "785203", "785260", "785263", "785350", "785430", "785470", "787030", "787050",
            "787080", "787110", "787300", "787450", "788580", "788610", "788620", "788660", "788970",
            "789050", "789060", "789250", "789510", "789580", "789820", "789880", "789900", "800010",
            "804020", "804030", "804050"];
        #for temperature data
        $valid_gulfStations = ["720273", "722010", "722014", "722015", "722016", "722034", "722038",
            "722041", "722064", "722104", "722106", "722108", "722110", "722115", "722116", "722159",
            "722200", "722209", "722320", "722351", "722406", "722408", "722420", "722422", "722423",
            "722427", "722435", "722436", "722500", "722505", "722510", "722515", "722516", "722524",
            "722527", "722543", "722555", "722682", "747880", "763993", "765480", "765491", "765493",
            "765494", "765905", "765906", "766127", "766440", "766443", "766491", "766493", "766870",
            "766910", "766913", "766920", "766950", "782240", "782290", "783250", "783284"];
        $valid_stations = array_merge($valid_caribbeanStations, $valid_gulfStations);


        #I know needs to change :)
        $this->path = "C:/xampp/htdocs/webdav";
        $rainDataLinks = $this->findDataLinksRain($valid_caribbeanStations);



        echo "<pre>";
        var_dump($rainDataLinks);
        echo "</pre>";
    }
    private function findDataLinksRain($valid_caribbeanStations) {
        $dataLinks = [];
        #get the paths to the valid stations
        foreach (scandir($this->path) as $station) {
            if (in_array($station, $valid_caribbeanStations)) { #ONLY CHECKS CARIB STATIONS BECAUSE ONLY READ RAIN THERE
                if (is_dir($this->path . '/' . $station) && !in_array($station, ['index.php', '.', '..'])) {
                    $link = $this->path . '/' . $station;
                    foreach (scandir($link) as $dateInLink)
                        if(!in_array($dateInLink, ['index.php', '.', '..']) && $dateInLink == "2018-02-01") {#$dateInLink == $this->currentDate) {
                            $dataLinks[] = $link."/".$dateInLink;
                        }
                }
            }

        }
        return $dataLinks;
    }
}

$d = new DataGetter();
