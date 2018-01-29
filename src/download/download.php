<?php
/*search main map for all valid station ID's
make array with all valid station ID's folders
loop through each map to find the folders that are max 6 days old
get all the json files of those folders
merge all the json files into 1 json file*/

date_default_timezone_set('Europe/Amsterdam');
$currentDate = date('m/d/Y ', time());
$start = "/Users/Jelmer/Documents/School/Jaar 2/2.2/projecten/testData/"; /*need to change ofcourse*/
$stations = array();
$valid_stations = array("945510","111111");
$stationLinks = array();
$dataLinks = array();
$directories = glob($start . "/*", GLOB_ONLYDIR);
foreach ($directories as $dir) {
    $dirParts = explode("/", $dir);
    $stations[] = $dirParts[10];
}
foreach ($stations as $station) {
    if(in_array($station,$valid_stations)) {
        /*can insert an if to check if it is the selected station*/
        $stationLinks[] = $start.$station;
    }
}

foreach ($stationLinks as $stL) {
    $files = scandir($stL);
    #var_dump($files);
    $realFiles = array();
    foreach ($files as $file) {
        if(substr($file, -5) == ".json") {
            $realFiles[] = $file;
        }
    }
}
foreach ($realFiles as $rFile) {
    $date = substr($rFile, 0, 10);
    $fileDateVals = explode("-", $date);
    #var_dump($fileDateVals);
    $dayOfFile = mktime(0, 0, 0, $fileDateVals[1], $fileDateVals[0], $fileDateVals[2]);

    $currentDateVals = dateVals($currentDate,"/");
    $maxLastDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1] - 6, $currentDateVals[2]);
    $currentDay = mktime(0, 0, 0, $currentDateVals[0], $currentDateVals[1], $currentDateVals[2]);

    if($dayOfFile >= $maxLastDay and $dayOfFile <= $currentDay) {
        $dataLinks[] = $stL."/".$rFile;
    }
}


/*[0] is month, [1] is day, [2] is year*/
function dateVals($dateString, $del) {
    $dateVals = explode($del,$dateString);
    $returnArray = array();
    foreach ($dateVals as $val) {
        $returnArray[] = (int)$val;
    }
    return $returnArray;
}


$jsonFiles = array();
foreach ($dataLinks as $link) {
    $file = file_get_contents($link);
    $json = json_decode($file, true);

    if ($json == null) {
        echo "Not valid Json, ERROR CODE:\n";
        echo json_last_error();
        exit;
    } else {
        $jsonFiles[] = $json;
    }
}
$downloadJson = json_encode($jsonFiles);
$filename=  date("d-M-Y", $maxLastDay) . "-" . date("d-M-Y", $currentDay);
header("Content-type: application/json");
header("Content-disposition: attachment; filename=$filename.json");
echo $downloadJson;

