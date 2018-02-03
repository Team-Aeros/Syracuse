
	<script src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7&key=AIzaSyAHfR2pf4ZcO5i68p1prZGq21B02DVmoik&language=en"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="http://code.jquery.com/jquery-latest.min.js"></script> 

	<script type="text/javascript">
	
	var map;
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

function bindInfoWindow(marker, map, infowindow, details) {
		google.maps.event.addListener(marker, 'click', function () {
		map.setZoom(6);
        map.setCenter(marker.getPosition());
		infowindow.setContent(details);
        infowindow.open(map, marker);
		var naam = details.substring(0,16);
		var ctx = document.getElementById('myChart').getContext('2d');
		var myChart  = new Chart(ctx, {
			// The type of chart we want to create
			type: 'line',

			// The data for our dataset
			data: {

				responsive: true,
				labels: [1,2,3,4,5,6,7,8,9,10,
					11,12,13,14,15,16,17,18,19,
					20,21,22,23,24,25,26,27,28,
					29,30,31,32,33,34,35,36,37,
					38,39,40,41,42,43,44,45,46,
					47,48,49,50,51,52,53,54,55,
					56,57,58,59,60],
				datasets: [{
					label: naam,
					backgroundColor: 'rgb(238, 127, 55)',
					borderColor: 'rgb(43, 133, 59)',
					display: true,

					responsive: true,

					fill: false,
					data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
						11, 12, 13, 14, 15, 16, 17, 18, 19,
						20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
						30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
						40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
						50, 51, 52, 53, 54, 55, 56, 57, 58, 59,

						60]

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
							labelString: 'Degrees in celcius'
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
    });
}
function content(elem) {
	    var td1 = document.getElementById("td1");
	    td1.style.backgroundColor = "white";

        var td2 = document.getElementById("td2");
        td2.style.backgroundColor = "white";

        var td3 = document.getElementById("td3");
        td3.style.backgroundColor = "white";

        var td4 = document.getElementById("td4");
        td4.style.backgroundColor = "white";

        var td5 = document.getElementById("td5");
        td5.style.backgroundColor = "white";

        var td6 = document.getElementById("td6");
        td6.style.backgroundColor = "white";

        var td7 = document.getElementById("td7");
        td7.style.backgroundColor = "white";

        var td8 = document.getElementById("td8");
        td8.style.backgroundColor = "white";

        var td9 = document.getElementById("td9");
        td9.style.backgroundColor = "white";

        var td10 = document.getElementById("td10");
        td10.style.backgroundColor = "white";

	    elem.style.backgroundColor = "#3e6846";

	    var selStat = document.getElementById("selStat");
	    selStat.innerHTML = "station selected: " + elem.innerHTML;
}

        
		</script>
	<body>	
    <div id="container">

        <div class="subcontainer" id="rain">
            <div id="list_1" class="textBlock widget">
                <div class="currentStation" id="currentStation">
                    <P id="selStat">{{ _translate('selStat') }}</P>
                </div>
                <h2>{{ _translate('rainListTitle') }}</h2>
                <table id="top10TableList">
                    <p>test</p>
                    <tr>
                        <td id="td1" onclick="content(this)">1</td>
                        <td id="val1"></td>
                    </tr>
                    <tr>
                        <td id="td2" onclick="content(this)">2</td>
                        <td id="val2"></td>
                    </tr>
                    <tr>
                        <td id="td3" onclick="content(this)">3</td>
                        <td id="val3"></td>
                    </tr>
                    <tr>
                        <td id="td4" onclick="content(this)">4</td>
                        <td id="val4"></td>
                    </tr>
                    <tr>
                        <td id="td5" onclick="content(this)">5</td>
                        <td id="val5"></td>
                    </tr>
                    <tr>
                        <td id="td6" onclick="content(this)">6</td>
                        <td id="val6"></td>
                    </tr>
                    <tr>
                        <td id="td7" onclick="content(this)">7</td>
                        <td id="val7"></td>
                    </tr>
                    <tr>
                        <td id="td8" onclick="content(this)">8</td>
                        <td id="val8"></td>
                    </tr>
                    <tr>
                        <td id="td9" onclick="content(this)">9</td>
                        <td id="val9"></td>
                    </tr>
                    <tr>
                        <td id="td10" onclick="content(this)">10</td>
                        <td id="val10"></td>
                    </tr>
                </table>
            </div>

            <div id="listData" class="textBlock widget">
                <H2>{{ _translate('rainDataTitle') }}</H2>
                <P> no data :(</P>
            </div>
        </div>

        <div class="subcontainer" id="temp">
            <div id="map" class="widget">
                <div id="default" style="width:100%; height:100%"></div>
            </div>

            <div id="graph" class="textBlock widget">

				<canvas id="myChart"></canvas>

            </div>
        </div>

        <br class="clear" />

      </div>
	</body>

