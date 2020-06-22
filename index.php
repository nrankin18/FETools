<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FE Tools</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="http://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<body>

<!-- Header -->
<h1>Sector File Generator</h1>
<hr>
<div class="options">
    <span>
        <button onclick="showNewFile()" class="menu">Create a sector file</button>
        <button onclick="showEditFile()" class="menu">Convert .sct2 to .kml</button>
        <button onclick="window.open('help.php')" class="menu">Learn how to use this tool</button>
    </span>
</div>

<div id="editFile" class="editWindow">
    <h3>Convert existing sector file sections into a Google Earth KML format by pasting them below: </h3>
    <div>
        <textarea id="reverseTxt" rows="10" cols="50" placeholder="Sector file text"></textarea><br>
        <button onclick="genKMLFile()">Generate KML file</button>
    </div>
</div>
</html>

<!-- >
<div id="newFile" class="editWindow">


    <h3>First, enter some basic information about the sector:</h3>
    <div>
        <label for="info-name">Sector File Name: </label><input type="text" id="info-name" placeholder="Boston Tower v5.0"><br>
        <label for="info-callsign">Default Callsign: </label><input type="text" id="info-callsign" placeholder="BOS_TWR"><br>
        <label for="info-airport">Default Airport: </label><input type="text" id="info-airport" maxlength="4" placeholder="KBOS"><br>
        <label for="info-latitude">Default Latitude: </label><input type="text" id="info-latitude" maxlength="14" placeholder="N042.20.54.750"><br>
        <label for="info-longitude">Default Longitude: </label><input type="text" id="info-longitude" maxlength="14" placeholder="W071.00.21.920"><br>
        <label for="info-nmLat">Nautical Miles per Degree of Latitude: </label><input type="text" id="info-nmLat" placeholder="60" value="60"><br>
        <label for="info-nmLong">Nautical Miles per Degree of Longitude: </label><input type="text" id="info-nmLong" placeholder="45"><br>
        <label for="info-mv">Magnetic Variation: </label><input type="text" id="info-mv" placeholder="16"><br>
        <label for="info-scale">Sector Scale Value: </label><input type="text" id="info-scale" placeholder="1" pattern="[0-9]*(\.[1-9]+)?" value="1"><br>
    </div>
*
    <h3>Next, paste the sector folder from Google Earth:</h3>
    <div>
        <textarea id="root-KML" rows="10" cols="50" placeholder='Sector folder from Google Earth (KML)'></textarea><br>
        <label for="default-sid-color">Default SID/STAR Color (optional): </label><input type="text" id="default-sid-color" placeholder="WHITE"><br>
        <label for="default-label-color">Default Label Color (optional): </label><input type="text" id="default-label-color" placeholder="WHITE"><br>
    </div>

    <h3>Optionally, you can upload NavData from Navigraph:</h3>
    <div>
        <label for="lat-center">Center of Sector: </label>
        <input type="text" id="lat-center" maxlength="14" placeholder="42.7170422">
        <input type="text" id="long-center" maxlength="14" placeholder="-71.1235333"><br>
        <span>Include all VORs, NDBs, fixes, airways and airports within a </span>
        <input type="text" id="radius" maxlength="3" placeholder="100">
        <span> nm radius</span><br>
        <label for="airports-txt">Upload Airports.txt: </label><input type="file" id="airports-txt" accept=".txt"> <br>
        <label for="navaids-txt">Upload Navaids.txt: </label><input type="file" id="navaids-txt" accept=".txt"> <br>
        <label for="waypoints-txt">Upload Waypoints.txt: </label><input type="file" id="waypoints-txt" accept=".txt"> <br>
        <label for="ats-txt">Upload ATS.txt: </label><input type="file" id="ats-txt" accept=".txt"> <br><br>
    </div>


</body>
</html>

<div id="newFile" class="editWindow">

    <h3>First, enter some basic information about the sector:</h3>
    <div>
        <label for="info-name">Sector File Name: </label><input type="text" id="info-name" placeholder="Boston Tower v5.0"><br>
        <label for="info-callsign">Default Callsign: </label><input type="text" id="info-callsign" placeholder="BOS_TWR"><br>
        <label for="info-airport">Default Airport: </label><input type="text" id="info-airport" maxlength="4" placeholder="KBOS"><br>
        <label for="info-latitude">Default Latitude: </label><input type="text" id="info-latitude" maxlength="14" placeholder="N042.20.54.750"><br>
        <label for="info-longitude">Default Longitude: </label><input type="text" id="info-longitude" maxlength="14" placeholder="W071.00.21.920"><br>
        <label for="info-nmLat">Nautical Miles per Degree of Latitude: </label><input type="text" id="info-nmLat" placeholder="60" value="60"><br>
        <label for="info-nmLong">Nautical Miles per Degree of Longitude: </label><input type="text" id="info-nmLong" placeholder="45"><br>
        <label for="info-mv">Magnetic Variation: </label><input type="text" id="info-mv" placeholder="16"><br>
        <label for="info-scale">Sector Scale Value: </label><input type="text" id="info-scale" placeholder="1" pattern="[0-9]*(\.[1-9]+)?" value="1"><br>
    </div>

    <h3>Next, paste the sector folder from Google Earth:</h3>
    <div>
        <textarea id="root-KML" rows="10" cols="50" placeholder='Sector folder from Google Earth (KML)'></textarea><br>
        <label for="default-sid-color">Default SID/STAR Color (optional): </label><input type="text" id="default-sid-color" placeholder="WHITE"><br>
        <label for="default-label-color">Default Label Color (optional): </label><input type="text" id="default-label-color" placeholder="WHITE"><br>
    </div>

    <h3>Optionally, you can upload NavData from Navigraph:</h3>
    <div>
        <label for="lat-center">Center of Sector: </label>
        <input type="text" id="lat-center" maxlength="14" placeholder="42.7170422">
        <input type="text" id="long-center" maxlength="14" placeholder="-71.1235333"><br>
        <span>Include all VORs, NDBs, fixes, airways and airports within a </span>
        <input type="text" id="radius" maxlength="3" placeholder="100">
        <span> nm radius</span><br>
        <label for="airports-txt">Upload Airports.txt: </label><input type="file" id="airports-txt" accept=".txt"> <br>
        <label for="navaids-txt">Upload Navaids.txt: </label><input type="file" id="navaids-txt" accept=".txt"> <br>
        <label for="waypoints-txt">Upload Waypoints.txt: </label><input type="file" id="waypoints-txt" accept=".txt"> <br>
        <label for="ats-txt">Upload ATS.txt: </label><input type="file" id="ats-txt" accept=".txt"> <br><br>
    </div>

    <script>
        $(document).ready(function(){
            $("#airports-txt").change(function(){
                readAirports();
            });
            $("#navaids-txt").change(function(){
                readNavaids();
            });
            $("#waypoints-txt").change(function(){
                readWaypoints();
            });
            $("#ats-txt").change(function(){
                readATS();
            });
            $("#lat-center").change(function(){
                readAirports();
                readNavaids();
                readWaypoints();
                readATS();
            });
            $("#long-center").change(function(){
                readAirports();
                readNavaids();
                readWaypoints();
                readATS();
            });
            $("#radius").change(function(){
                readAirports();
                readNavaids();
                readWaypoints();
                readATS();
            });
        });
    </script>

    <button onclick="genNewFile()">Generate File</button>
</div>