# Data Handling
This web-application displays json data saved in a file system.
Currently the data that is displayed can be divided into 2 sets, the Rain dataset and the Temperature dataset.

##The Rain dataset
The Rain dataset contains data from the current day, this data includes: Station name, StationID, Precipitation(amount of rain), wind speed and temperature. <br><br>
The top 10 stations with the highest precipitation are displayed in a table as Station name - precipitation,
when clicked on a station name the rest of the data that belongs to that station is displayed.

##The Temperature dataset
The Temperature dataset contains data from the past hour, this data includes: StationID, Temperature.<br><br>
On the website under the temperature tab is a map shown on the left. In this map there is a marker for each on of our stations in the Gulf of Mexico. When clicked on a marker the graph on the right is filled with the Temperature data of the selected station.

##The getting of the data
The task of getting the required data falls to the DataGetter class. This class can read the entries in the webdav folder in our file system.
Depending on the required data it can get the paths to the json files required for the Rain dataset or the Temperature dataset.
It then uses the paths to read the json files, get the required data and put it into an array.
The syntax for a filled array for the Rain dataset is:<br>
Array(
Key (the stationID) => Value(array containing Station name, StationID, Precipitation, wind speed and temperature)
)
<br><br>
The syntax for a filled array for the Temperature dataset is:<br>
Array(
Key (the stationID) => Value(array containing temperature values of the last hour)
)