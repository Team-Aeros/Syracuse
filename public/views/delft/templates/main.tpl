
	<script src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7&key=AIzaSyAHfR2pf4ZcO5i68p1prZGq21B02DVmoik&language=en"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<script type="text/javascript">

     var dataArray = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
        11, 12, 13, 14, 15, 16, 17, 18, 19,
        20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
        30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
        40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
        50, 51, 52, 53, 54, 55, 56, 57, 58, 59,
        60];

     function changeData() {
         dataArray = [21, 22, 23,21, 22, 23,21, 22, 23, 20,
             21, 22, 23,21, 22, 23,21, 22, 23, 20,
             21, 22, 23,21, 22, 23,21, 22, 23, 20,
             21, 22, 23,21, 22, 23,21, 22, 23, 20,
             21, 22, 23,21, 22, 23,21, 22, 23, 20,
             21, 22, 23,21, 22, 23,21, 22, 23, 20];
     }

	let map;
	function initialize() {

    var myOptions = {
        center: new google.maps.LatLng(23.300153, -86.770968), 
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
		
    };
   	map = new google.maps.Map(document.getElementById("default"), 
		myOptions);

     $.getJSON('{{ base_url }}/weatherstations.json', function(json1) {
     $.each(json1.weatherstations, function (key, data) {

        var latLng = new google.maps.LatLng(data.lat, data.lng);

        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: data.title
        });
		 
        var details = data.title;
		
		var infowindow = new google.maps.InfoWindow();
			
		infowindow.setContent(details);
        infowindow.open(map,marker);


        bindInfoWindow(marker, map, infowindow, details);

            });

    });

}
function loadGraph() {
     ctx = document.getElementById('myChart').getContext('2d');
     myChart  = new Chart(ctx, {
        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: {

            responsive: true,
            labels:
                ["60 Minutes ago","","","","","","53 Minutes ago",
                    "","","","","","47 Minutes ago",
                    "","","","","","41 Minutes ago",
                    "","","","","","35 Minutes ago",
                    "","","","","","29 Minutes ago",
                    "","","","","","23 Minutes ago",
                    "","","","","","17 Minutes ago",
                    "","","","","","11 Minutes ago",
                    "","","","","","6 Minutes ago",
                    "","","","","","Right Now"],
            datasets: [{
                label: naam,
                backgroundColor: 'rgb(238, 127, 55)',
                borderColor: 'rgb(43, 133, 59)',
                display: true,

                responsive: true,

                fill: false,
                data: dataArray

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

     var intervalIK = window.setInterval(destroyGraph, 14990);
     var intervalID = window.setInterval(loadGraph, 15000);


     function destroyGraph() {
         myChart.destroy();
     }

function bindInfoWindow(marker, map, infowindow, details) {
		google.maps.event.addListener(marker, 'click', function () {
		map.setZoom(6);
        map.setCenter(marker.getPosition());
		infowindow.setContent(details);
        infowindow.open(map, marker);
		naam = details.substring(0,16);
		loadGraph();

    });
}

function content(elem) {
    var tds = ['td1','td2','td3','td4','td5','td6','td7','td8','td9','td10'];
    var tdVals = ['td1Val','td2Val','td3Val','td4Val','td5Val','td6Val','td7Val','td8Val','td9Val','td10Val'];
    for (i=0; i < tds.length; i++) {
        var td = document.getElementById(tds[i]);
        var tdVal = document.getElementById(tdVals[i]);
        td.style.backgroundColor = "white";
        td.style.color = "black";
        tdVal.style.backgroundColor = "white";
    }
    elem.style.backgroundColor = "#CDD8C5";
    var selStat = document.getElementById("selStat");
    var tdText = elem.innerHTML.split(":");
    var stat = tdText[0];
    display_rain_data('/index.php/update/ajax/top10', stat);

}
        
		</script>
	<body>	
    <div id="container">
            <div class="subcontainer" id="rain">
                <div id="list_1" class="textBlock widget">
                    <h2>{{ _translate('rainListTitle') }}</h2>
                    <table id="listTable">
                        <tr>
                            <td id="td1" class="tableRow" onclick="content(this)"></td>
                            <td id="td1Val"></td>
                        </tr>
                        <tr>
                            <td id="td2" class="tableRow" onclick="content(this)"></td>
                            <td id="td2Val"></td>
                        </tr>
                        <tr>
                            <td id="td3" class="tableRow" onclick="content(this)"></td>
                            <td id="td3Val"></td>
                        </tr>
                        <tr>
                            <td id="td4" class="tableRow" onclick="content(this)"></td>
                            <td id="td4Val"></td>
                        </tr>
                        <tr>
                            <td id="td5" class="tableRow" onclick="content(this)"></td>
                            <td id="td5Val"></td>
                        </tr>
                        <tr>
                            <td id="td6" class="tableRow" onclick="content(this)"></td>
                            <td id="td6Val"></td>
                        </tr>
                        <tr>
                            <td id="td7" class="tableRow" onclick="content(this)"></td>
                            <td id="td7Val"></td>
                        </tr>
                        <tr>
                            <td id="td8" class="tableRow" onclick="content(this)"></td>
                            <td id="td8Val"></td>
                        </tr>
                        <tr>
                            <td id="td9" class="tableRow" onclick="content(this)"></td>
                            <td id="td9Val"></td>
                        </tr>
                        <tr>
                            <td id="td10" class="tableRow" onclick="content(this)"></td>
                            <td id="td10Val"></td>
                        </tr>
                    </table>
                </div>

                <div id="listData" class="textBlock widget">
                    <H2>{{ _translate('rainDataTitle') }}</H2>
                    <table id="rainDataVals">
                        <tr>
                            <td>Station: </td>
                            <td id="station"></td>
                        </tr>
                        <tr>
                            <td>Rain: </td>
                            <td id="rainVal"></td>
                        </tr>
                        <tr>
                            <td>Temperature: </td>
                            <td id="tempVal"></td>
                        </tr>
                        <tr>
                            <td>Wind speed: </td>
                            <td id="wspeedVal"></td>
                        </tr>
                    </table>
                </div>
            </div>
        <script>
            load_rain_data('/index.php/update/ajax/top10');
        </script>


        <div class="subcontainer" id="temp">
            <div id="map" class="widget">
                <div id="default" style="width:100%; height:100%"></div>
            </div>
            <button onclick="changeData()">Change me</button>
            <div id="graph" class="textBlock widget">
				<canvas id="myChart"></canvas>
            </div>
        </div>

        <br class="clear" />

      	</div>
	</body>

