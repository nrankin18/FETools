<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FE Tools</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="http://code.jquery.com/jquery-3.4.1.js"></script>
    <script src="scripts.js"></script>
</head>

<body>
    <img src="logo.png" class="logo">
    <!-- Header -->
    <h1>Sector File Generator</h1>
    <hr>
    <div class="menu">
        <span>
            <button onclick="showCreateWindow()" class="menu">Create a sector file</button>
            <button onclick="showConvertWindow()" class="menu">Convert .sct2 to .kml</button>
            <button class="menu" disabled>Help</button>
        </span>
    </div>

    <div id="createWindow" class="inputWindow">
        <h3>Enter some basic information about the sector:</h3>
        <div>
            <label for="infoName">Sector File Name: </label><input type="text" id="infoName" placeholder="Boston Tower v5.0"><br>
            <label for="infoCallsign">Default Callsign: </label><input type="text" id="infoCallsign" placeholder="BOS_TWR"><br>
            <label for="infoAirport">Default Airport: </label><input type="text" id="infoAirport" maxlength="4" placeholder="KBOS"><br>
            <label for="infoLat">Default Latitude: </label><input type="text" id="infoLat" maxlength="14" placeholder="N042.20.54.750"><br>
            <label for="infoLong">Default Longitude: </label><input type="text" id="infoLong" maxlength="14" placeholder="W071.00.21.920"><br>
            <label for="infoNMLat">Nautical Miles per Degree of Latitude: </label><input type="text" id="infoNMLat" placeholder="60" value="60"><br>
            <label for="infoNMLong">Nautical Miles per Degree of Longitude: </label><input type="text" id="infoNMLong" placeholder="45"><br>
            <label for="infoMV">Magnetic Variation: </label><input type="text" id="infoMV" placeholder="16"><br>
            <label for="infoScale">Sector Scale Value: </label><input type="text" id="infoScale" placeholder="1" value="1"><br>
        </div>
        <h3>Paste the sector folder from Google Earth:</h3>
        <div>
            <textarea id="kmlText" rows="10" cols="50" placeholder='Sector folder from Google Earth (KML)'></textarea><br>
        </div>
        <h3>Optionally, you can upload NavData from Navigraph:</h3>
        <div>
            <label for="navLatCenter">Center of Sector: </label>
            <input type="text" id="navLatCenter" maxlength="14" placeholder="42.7170422">
            <input type="text" id="navLongCenter" maxlength="14" placeholder="-71.1235333"><br>
            <span>Include all VORs, NDBs, fixes, airways and airports within a </span>
            <input type="text" id="navRadius" maxlength="3" placeholder="100">
            <span> nm radius</span><br>
            <label for="navAirports">Upload Airports.txt: </label><input type="file" id="navAirports" accept=".txt"> <br>
            <label for="navNavaids">Upload Navaids.txt: </label><input type="file" id="navNavaids" accept=".txt"> <br>
            <label for="navWaypoints">Upload Waypoints.txt: </label><input type="file" id="navWaypoints" accept=".txt"> <br>
            <label for="navATS">Upload ATS.txt: </label><input type="file" id="navATS" accept=".txt"> <br><br>
        </div>

        <br>
        <button id="createSubmit" onclick="createFile()">Generate File</button>
        <div class="loader" id="createLoader" style="display:inline-block"></div>
        <div style="display:inline-block" id="createStatus" class="status">status</div>
    </div>

    <div id="convertWindow" class="inputWindow">
        <h3>Convert an existing sector file into a Google Earth KML format by pasting it below or uploading the file: </h3>
        <div>
            <textarea id="sctText" rows="10" cols="50" placeholder="Paste sector file text here"></textarea>
            <div>Or upload the sector file:</div>
            <input type="file" id="sctFile" accept=".sct2">
        </div>
        <br>
        <button onclick="convertFile()" id="convertSubmit">Generate KML file</button>
        <div class="loader" id="convertLoader" style="display:inline-block"></div>
        <div style="display:inline-block" id="convertStatus" class="status">status</div>
    </div>

</html>