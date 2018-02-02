<?php
/**
 * Created by PhpStorm.
 * User: Jelmer
 * Date: 02-Feb-18
 * Time: 11:33
 */

use Syracuse\src\download\DataReader as DataReader;
/*
 * Makes a data getter object,
 * when update() is called it returns an array of the top10Rain of the last day for all the carribbean stations and
 * the last temperature of the gulf stations. Top10Rain = [0], Temperature = [1]
 */
class DataGetter {
    private $goodLinks;
    private $currentDate;
    private $dataReader;
    public function __construct() {
        $this->gooddLinks = [];
        $this->dataReader = new DataReader();
        $dlinks = $this->dataReader->getDataLinks();
        $currentDate = "2018-02-01";#$dataReader->getCurrentDate('Europe/Amsterdam');
        $currentDateVals = explode("-",$currentDate);
        #var_dump($currentDateVals);
        $this->currentDay = mktime(0, 0, 0, $currentDateVals[2], $currentDateVals[1], $currentDateVals[0]);
        foreach ($dlinks as $link) {
            $date = substr($link,-24, 10);
            if($currentDate === $date) {
                $this->goodLinks[] = $link;
            } else {
                exit;
            }
        }
    }
    public function update() {
        $top10Rain = [];
        $temperature = [];
        foreach ($this->goodLinks as $dataLink) {
            $linkparts = explode("/", $dataLink);
            #var_dump($linkparts);
            $json = file_get_contents($dataLink);
            $file = json_decode($json, true);
            if ($file['date'] = $this->currentDate - 1) {
                $fileData = ["station" => $file['station'], "date" => $file['date'], "time" => $file['time'],
                    "temperature" => $file['temperature'], "wind_speed" => $file['wind_speed'], "rain" => $file['precipitation']];
                if(in_array($linkparts[9], $this->dataReader->getCaribStations())) {
                    #update the top 10 rain
                    if (!empty($top10Rain)) {
                        foreach ($top10Rain as $key => $val) {
                            /*echo "<br>";
                            echo $key;
                            echo "<br>";*/
                            if (!array_key_exists($fileData['station'], $top10Rain)) {
                                $top10Rain[$fileData['station']] = $fileData["rain"];
                            } else if ($top10Rain[$key] < $val) {
                                $top10Rain[$fileData['station']] = $fileData["rain"];
                            }
                        }
                    } else {
                        $top10Rain[$fileData['station']] = $fileData["rain"];
                    }

                } elseif(in_array($linkparts[9], $this->dataReader->getGulfStations())) {
                    #update the temperature
                    if (!empty($temperature)) {
                        foreach ($temperature as $key => $val) {
                            $temperature[$fileData['station']] = $fileData["temperature"];
                        }
                    } else {
                        $temperature[$fileData['station']] = $fileData['temperature'];
                    }
                }
            }

        }
        asort($top10Rain);
        $top10Rain = array_reverse($top10Rain, true);
        $top10Rain = array_slice($top10Rain, 0, 10, true);

        return array($top10Rain, $temperature);
    }

    public function getDataForStation($station) {
        foreach ($this->goodLinks as $dataLink) {
            $linkparts = explode("/", $dataLink);
            #var_dump($linkparts);
            $json = file_get_contents($dataLink);
            $file = json_decode($json, true);
            if ($file['date'] = $this->currentDate - 1) {
                $fileData = ["station" => $file['station'], "date" => $file['date'], "time" => $file['time'],
                    "temperature" => $file['temperature'], "wind_speed" => $file['wind_speed'], "rain" => $file['precipitation']];
                if (in_array($linkparts[9], $this->dataReader->getCaribStations())) {
                    return array($fileData['station'], $fileData['wind_speed'], $fileData['temperature'], $fileData['rain']);
                }
            }
    }


}