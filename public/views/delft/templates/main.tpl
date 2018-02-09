	<script src="https://maps.google.com/maps/api/js?sensor=false&amp;libraries=geometry&amp;v=3.7&amp;key=AIzaSyAHfR2pf4ZcO5i68p1prZGq21B02DVmoik&amp;language=en"></script>
	<script type="text/javascript">


	let map;
	function initialize() {
        startUp(map);
    }


        //var intervalID = window.setInterval(loadGraph('/index.php/update/ajax/tempGraph', naam), 1000);//60000);

function content(elem) {
    var tds1 = ['td1','td3','td5','td7','td9'];
    var tds2 = ['td2','td4','td6','td8','td10'];
    var tds = tds1.concat(tds2);
    var tdsVals1 = ['td1Val','td3Val','td5Val','td7Val','td9Val'];
    var tdsVals2 = ['td2Val','td4Val','td6Val','td8Val','td10Val'];
    var tdVals = tdsVals1.concat(tdsVals2);
    for (i=0; i < tds.length; i++) {
        var color = "";
        var td = document.getElementById(tds[i]);
        var tdVal = document.getElementById(tdVals[i]);
        if(tds1.includes(tds[i])) {
            color = "#e8ebef"; //e8ebef
        } else if(tds2.includes(tds[i])) {
            color = "#c3d1e5"; //c3d1e5
        }
        td.style.backgroundColor = color;
        tdVal.style.backgroundColor = color;
    }
    var selTdVal = document.getElementById(elem.id + "Val");
    selTdVal.style.backgroundColor = "#979a9e"; //979a9e
    elem.style.backgroundColor = "#979a9e";

    var tdText = elem.innerHTML.split(":");
    var stat = tdText[0];
    display_rain_data('/index.php/update/ajax/top10', stat);

}
        
		</script>
    <div id="container">
            <div class="subcontainer" id="rain">
                <div id="list_1" class="textBlock widget">
                    <h2>{{ _translate('rainListTitle') }}</h2>
                    <table id="listTable">
                        <tr class="tableHead">
                            <td>Station name</td>
                            <td>Total rain this day</td>
                        </tr>
                        <tr id= "row1" class="tableRow">
                            <td id="td1" onclick="content(this)"></td>
                            <td id="td1Val"></td>
                        </tr>
                        <tr id = "row2" class="tableRow2">
                            <td id="td2"  onclick="content(this)"></td>
                            <td id="td2Val"></td>
                        </tr>
                        <tr id = "row3" class="tableRow">
                            <td id="td3"  onclick="content(this)"></td>
                            <td id="td3Val"></td>
                        </tr>
                        <tr id = "row4" class="tableRow2">
                            <td id="td4"  onclick="content(this)"></td>
                            <td id="td4Val"></td>
                        </tr>
                        <tr id = "row5" class="tableRow">
                            <td id="td5"  onclick="content(this)"></td>
                            <td id="td5Val"></td>
                        </tr>
                        <tr id = "row6" class="tableRow2">
                            <td id="td6"  onclick="content(this)"></td>
                            <td id="td6Val"></td>
                        </tr>
                        <tr id = "row7" class="tableRow">
                            <td id="td7"  onclick="content(this)"></td>
                            <td id="td7Val"></td>
                        </tr>
                        <tr id = "row8" class="tableRow2">
                            <td id="td8"  onclick="content(this)"></td>
                            <td id="td8Val"></td>
                        </tr>
                        <tr id = "row9" class="tableRow">
                            <td id="td9"  onclick="content(this)"></td>
                            <td id="td9Val"></td>
                        </tr>
                        <tr id = "row10" class="tableRow2">
                            <td id="td10"  onclick="content(this)"></td>
                            <td id="td10Val"></td>
                        </tr>
                    </table>
                </div>

                <div id="listData" class="textBlock widget">
                    <H2>{{ _translate('rainDataTitle') }}</H2>
                    <table id="rainDataVals">
                        <tr class="headRow">
                            <td>{{ _translate('name') }}</td>
                            <td>{{ _translate('value') }}</td>
                        </tr>
                        <tr class="darkRow">
                            <td id="stationHead">{{ _translate('table_station') }}</td>
                            <td id="station"></td>
                        </tr>
                        <tr class="lightRow">
                            <td id="rainHead">{{ _translate('table_rain') }}</td>
                            <td id="rainVal"></td>
                        </tr>
                        <tr class="darkRow">
                            <td id="tempHeadHigh">High Temperature: </td>
                            <td id="tempValH"></td>
                        </tr>
                        <tr class="lightRow">
                            <td id="tempHeadLow">Low Temperature: </td>
                            <td id="tempValL"></td>
                        </tr>
                        <tr class="darkRow">
                            <td id="wspeedHead">Wind speed: </td>
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

            <div id="graph" class="textBlock widget">
				<canvas id="myChart"></canvas>
            </div>
        </div>

        <br class="clear" />

      	</div>
	</body>

