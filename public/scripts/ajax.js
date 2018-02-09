/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

/**
 * Peforms an ajax request
 * @param ajax_url, the url
 * @param data_to_send, the data that needs to be send
 * @param handler, the handler
 * @param get_or_post, GET or POST default GET
 * @param data_type, the data type default html
 */
function perform_request(ajax_url, data_to_send, handler, get_or_post = 'GET', data_type = 'html') {
    $.ajax({
        data: data_to_send,
        dataType: data_type,
        url: base_url + ajax_url,
        method: get_or_post,
        success: handler,
        error: function() {
            alert(could_not_load_module);
        }
    });
}

/**
 * Loads a module
 * @param element, the element that must be filled
 * @param url, the url
 */
function load_module(element, url) {
    let request = perform_request(url, {}, function(message, data, response) {
        $(element).html(response.responseText);
    });
}

/**
 * loads the rain data for the top 10 table
 * @param url, the url
 */
function load_rain_data(url) {
    var tds = ['td1','td2','td3','td4','td5','td6','td7','td8','td9','td10'];
    var tdVals = ['td1Val','td2Val','td3Val','td4Val','td5Val','td6Val','td7Val','td8Val','td9Val','td10Val'];
    $("#td1").hide();
    $("#td2").hide();
    $("#td3").hide();
    $("#td4").hide();
    $("#td5").hide();
    $("#td6").hide();
    $("#td7").hide();
    $("#td8").hide();
    $("#td9").hide();
    $("#td10").hide();
    $("#td1Val").hide();
    $("#td2Val").hide();
    $("#td3Val").hide();
    $("#td4Val").hide();
    $("#td5Val").hide();
    $("#td6Val").hide();
    $("#td7Val").hide();
    $("#td8Val").hide();
    $("#td9Val").hide();
    $("#td10Val").hide();
    $("#stationHead").hide();
    $("#rainHead").hide();
    $("#tempHead").hide();
    $("#wspeedHead").hide();
    $("#station").hide();
    $("#rainVal").hide();
    $("#tempVal").hide();
    $("#wspeedVal").hide();
    perform_request(url, {}, function(message, data, response) {
        let i = 0;
        $.each(response.responseJSON, function() {
            //we know a bit weird but $("#tds[i]").show() somehow doesn't work
            $("#td1").show();
            $("#td2").show();
            $("#td3").show();
            $("#td4").show();
            $("#td5").show();
            $("#td6").show();
            $("#td7").show();
            $("#td8").show();
            $("#td9").show();
            $("#td10").show();
            $("#td1Val").show();
            $("#td2Val").show();
            $("#td3Val").show();
            $("#td4Val").show();
            $("#td5Val").show();
            $("#td6Val").show();
            $("#td7Val").show();
            $("#td8Val").show();
            $("#td9Val").show();
            $("#td10Val").show();
            if(this.station !== null) {
                if (tds[i] != undefined) {
                    document.getElementById(tds[i]).innerHTML = this.name + ':';
                    //document.getElementById(tds[i]).show();
                    document.getElementById(tdVals[i]).innerHTML = this.precipitation + " mm";
                    //document.getElementById(tdVals[i]).show();
                    i++;
                }
            }
        });
    }, 'GET', 'json');

}

/**
* Loads the graph
* @param url, the url
* @param station, the selected station marker
*/
function loadGraph(url, station) {
    var stationID = "" + station;
    perform_request(url, {}, function(message, data,response) {
        let dataArray = response.responseJSON;

        //console.log(dataArray);
        for(i=0;i < dataArray.length; i++) {

            var dataStation = dataArray[i][stationID];
            //console.log(stationID);
            //console.log(dataArray[i][stationID]);
            count = i;
            if (dataStation !== undefined) {
                break;
            }
        }

        var ghostCount = 60 - dataArray[count][stationID].length;
        var ghost = [];
        for (ghostCount; ghostCount > 0; ghostCount --) {
            ghost.push(null);
        }
        var graphData = ghost.concat(dataArray[i][stationID]);

        //console.log(graphData);
        myChart.destroy();
        ctx = document.getElementById('myChart').getContext('2d');

        myChart = new Chart(ctx, {

            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {

                responsive: true,
                labels:
                    ["60 Minutes ago", "", "", "", "", "", "53 Minutes ago",
                        "", "", "", "", "", "47 Minutes ago",
                        "", "", "", "", "", "41 Minutes ago",
                        "", "", "", "", "", "35 Minutes ago",
                        "", "", "", "", "", "29 Minutes ago",
                        "", "", "", "", "", "23 Minutes ago",
                        "", "", "", "", "", "17 Minutes ago",
                        "", "", "", "", "", "11 Minutes ago",
                        "", "", "", "", "", "6 Minutes ago",
                        "", "", "", "", "", "Right Now"],
                datasets: [{
                    label: stationID,
                    backgroundColor: 'rgb(238, 127, 55)',
                    borderColor: 'rgb(43, 133, 59)',
                    display: true,

                    responsive: true,

                    fill: false,
                    data: graphData

                }]
            },

            // Configuration options go here

            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Degrees in Celcius'
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Minutes'

                        }
                    }]

                }
            }
        });

    }, 'GET', 'json');
}


/**
 * Displays the rain data for the selected station
 * @param url, the url
 * @param station, the selected station
 */
function display_rain_data(url,station) {
    var stationNameDoc = station;
    perform_request(url, {}, function(message, data, response) {
        $.each(response.responseJSON, function() {
            if(stationNameDoc === this.name) {
                $("#stationHead").show();
                $("#rainHead").show();
                $("#tempHead").show();
                $("#wspeedHead").show();
                $("#station").show();
                $("#rainVal").show();
                $("#tempVal").show();
                $("#wspeedVal").show();

                var rainVal = document.getElementById("rainVal");
                var tempVal = document.getElementById("tempVal");
                var wspeedVal = document.getElementById("wspeedVal");
                var stationName = document.getElementById("station");

                rainVal.innerHTML = this.precipitation + " mm";
                tempVal.innerHTML = this.temperature + " °C";
                wspeedVal.innerHTML = this.wind_speed + " Km/H";
                stationName.innerHTML = this.name;
            }
        });
    }, 'GET', 'json');
}

function getStations(url, map) {
    perform_request(url, {}, function(message, data, response) {
        let dataArray = response.responseJSON;
        for(i=0; i<dataArray.length; i++) {
            details = dataArray[i];
            var latLng = new google.maps.LatLng(dataArray[i]['lat'], dataArray[i]['lng']);
            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: dataArray[i].name
            });

            var infowindow = new google.maps.InfoWindow();

            infowindow.setContent(details.name + ' (' + details.temp + '°c)');
            infowindow.open(map, marker);

            bindInfoWindow(marker, map, infowindow, details);
        }
    }, 'GET', 'json');
}

function getMarkTitle(url, stationID) {
    details = stationID;
    perform_request(url, {}, function(message, data, response) {
        let dataArray = response.responseJSON;
        //console.log(dataArray);
        for(i=0; i< dataArray.length; i++) {
            //console.log(i);
            var station = dataArray[i][stationID];
            if (station !== undefined) {
                details = "" + stationID + ": " + dataArray[i][stationID][dataArray[i][stationID].length -1];
                //console.log("ajax");
                //console.log(details);
                //document.getElementById("ghostDataTitle").innerHTML = "" + stationID + ": " + dataArray[i][stationID][dataArray[i][stationID].length -1];
                break;
            }
        }
    }, 'GET', 'json');
}
function bindInfoWindow(marker, map, infowindow, details) {
    google.maps.event.addListener(marker, 'click', function () {
        console.log(timerID);
        if(timerID === null) {
            console.log("starting timer");
            timerID = setInterval(function() {
                loadGraph('/index.php/update/ajax/tempGraph', details);
            }, 5000);
        } else {
            console.log("stopping timer");
            clearInterval(timerID);
            console.log("starting timer again");
            timerID = setInterval(function() {
                loadGraph('/index.php/update/ajax/tempGraph', details);
            }, 5000);
        }
        console.log("updating graph");
    map.setZoom(6);
    map.setCenter(marker.getPosition());
    infowindow.setContent(details);
    infowindow.open(map, marker);
    loadGraph('/index.php/update/ajax/tempGraph', details);
    });
}
function startUp(map) {
    timerID = null;

    var myOptions = {
        center: new google.maps.LatLng(23.300153, -86.770968),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("default"), myOptions);
    getStations('/index.php/update/ajax/stations', map);

    ctx = document.getElementById('myChart').getContext('2d');

    myChart = new Chart(ctx, {

        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: {

            responsive: true,
            labels:
                ["60 Minutes ago", "", "", "", "", "", "53 Minutes ago",
                    "", "", "", "", "", "47 Minutes ago",
                    "", "", "", "", "", "41 Minutes ago",
                    "", "", "", "", "", "35 Minutes ago",
                    "", "", "", "", "", "29 Minutes ago",
                    "", "", "", "", "", "23 Minutes ago",
                    "", "", "", "", "", "17 Minutes ago",
                    "", "", "", "", "", "11 Minutes ago",
                    "", "", "", "", "", "6 Minutes ago",
                    "", "", "", "", "", "Right Now"],
            datasets: [{
                label: "No station selected",
                backgroundColor: 'rgb(238, 127, 55)',
                borderColor: 'rgb(43, 133, 59)',
                display: true,

                responsive: true,

                fill: false,
                data: []

            }]
        },

        // Configuration options go here

        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Degrees in Celcius'
                    }
                }],
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Minutes'

                    }
                }]

            }
        }
    });
}





