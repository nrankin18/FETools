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
    <a href="https://bvartcc.com"><img src="img/logo.png" class="logo"></a>
    <!-- Header -->
    <h1>Facility Engineer Toolkit</h1>
    <hr>
    <div class="menu">
        <span>
            <button onclick="showCreateWindow()" class="menu">Create a sector file</button>
            <button onclick="showConvertWindow()" class="menu">Convert .sct2 to .kml</button>
            <button onclick="showMVAWindow()" class="menu">Convert FAA MVA/MIA</button>
            <button onclick="showHelpWindow()" class="menu">Help</button>
        </span>
    </div>

    <div id="createWindow" class="inputWindow">
        <h3>Enter some basic information about the sector:</h3>
        <div>
            <label for="infoName">Sector File Name: </label><input type="text" id="infoName" placeholder="Boston Tower v5.0"><br>
            <label for="infoCallsign">Default Callsign: </label><input type="text" id="infoCallsign" placeholder="BOS_TWR"><br>
            <label for="infoAirport">Default Airport: </label><input type="text" id="infoAirport" maxlength="4" placeholder="KBOS"><br>
            <label for="infoLat">Default Latitude: </label><input type="text" id="infoLat" maxlength="14" placeholder="42.7170422"><br>
            <label for="infoLong">Default Longitude: </label><input type="text" id="infoLong" maxlength="14" placeholder="-71.1235333"><br>
            <label for="infoNMLat">Nautical Miles per Degree of Latitude: </label><input type="text" id="infoNMLat" placeholder="60" value="60"><br>
            <label for="infoNMLong">Nautical Miles per Degree of Longitude: </label><input type="text" id="infoNMLong" placeholder="45"><br>
            <label for="infoMV">Magnetic Variation: </label><input type="text" id="infoMV" placeholder="16"><br>
            <label for="infoScale">Sector Scale Value: </label><input type="text" id="infoScale" placeholder="1" value="1"><br>
        </div>
        <h3>Paste the sector folder from Google Earth:</h3>
        <div>
            <textarea id="kmlText" rows="10" cols="50" placeholder='Sector folder from Google Earth (KML)'></textarea><br>
        </div>
        <label for="sectorColor">Default Map Color (can be left blank): </label><input type="text" id="sectorColor"><br>
        <label for="labelColor">Default Label Color (can be left blank): </label><input type="text" id="labelColor"><br>
        <!-- 
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
 -->
        <br>
        <button id="createSubmit" onclick="createFile()">Create Sector File</button>
        <div class="loader" id="createLoader" style="display:inline-block"></div>
        <div style="display:inline-block" id="createStatus" class="status">status</div>
    </div>

    <div id="convertWindow" class="inputWindow">
        <h3>Convert an existing sector file into a Google Earth KML format by pasting it below or uploading the file: </h3>
        <div>*Note that this tool is unable to parse diagrams that use NAVAIDs or fixes to define lines. All lines must be formatted with coordinates only.</div>
        <br>
        <div>
            <textarea id="sctText" rows="10" cols="50" placeholder="Paste sector file text here"></textarea>
            <div>Or upload the sector file:</div>
            <input type="file" id="sctFile" accept=".sct2">
        </div>
        <br>
        <button onclick="convertFile()" id="convertSubmit">Convert to KML file</button>
        <div class="loader" id="convertLoader" style="display:inline-block"></div>
        <div style="display:inline-block" id="convertStatus" class="status">status</div>
    </div>

    <div id="mvaWindow" class="inputWindow">
        <h3>Convert FAA published MIA/MVA charts to .kml: </h3>
        <div>FAA MIA/MVA maps are published <a href="https://www.faa.gov/air_traffic/flight_info/aeronav/digital_products/mva_mia/" target="_blank">here</a>. Please upload the .xml format of the video map to convert:</div>
        <br>
        <div>
            <input type="file" id="mvaFile" accept=".xml">
        </div>
        <br>
        <button onclick="convertMVA()" id="mvaSubmit">Convert to KML file</button>
        <div class="loader" id="mvaLoader" style="display:inline-block"></div>
        <div style="display:inline-block" id="mvaStatus" class="status">status</div>
    </div>

    <div id="helpWindow" class="inputWindow">
        <h2>Table of Contents:</h2>
        <ul>
            <li>
                <a href="#create">Creating a new Sector File</a>
                <ul>
                    <li><a href="#basic_info">Basic Information</a></li>
                    <li><a href="#ge_kml">Google Earth KML</a></li>
                    <li><a href="#artcc">The ARTCC Section</a></li>
                    <li><a href="#artcc_high">The ARTCC HIGH and ARTCC LOW Sections</a></li>
                    <li><a href="#sid">The SID and STAR Sections</a></li>
                    <li><a href="#geo">The GEO Section</a></li>
                    <li><a href="#regions">The REGIONS Section</a></li>
                    <li><a href="#labels">The LABELS Section</a></li>
                    <li><a href="#uploading_ge_data">Uploading Google Earth Data</a></li>
                    <!-- <li><a href="#navigraph">Navigraph NavData</a></li> -->
                </ul>
            </li>
            <li><a href="#convert">Converting a .sct2 File to a .kml File</a></li>
            <li><a href="#mva">Converting FAA MVA/MIA Files</a></li>
            <li><a href="#report">Reporting Issues or Suggestions</a></li>
        </ul>
        <h2 id="create">Creating a new Sector File:</h2>
        <h3 id="basic_info">Basic Information</h3>
        <p>Every sector file contains an info section that defines basic required parameters about the airspace represented by the sector file. In-depth documentation can be found on <a target="_blank" href="http://www1.metacraft.com/VRC/docs/doc.php?page=appendix_g">this</a> page under "The [INFO] Section".</p>
        <p>Note that all coordinates must be inputted in decimal format (42.717, -71.123) and will be converted automatically to the degree, minute, second format used by VRC.</p>
        <h3 id="ge_kml">Google Earth KML</h3>
        <p>The majority of the following sections are to be designed on Google Earth Pro then exported to this website. Google Earth is a free application available on PC, Mac, or Linux and can be downloaded <a target="blank" href="https://www.google.com/earth/versions/#download-pro ">here</a>.</p>
        <p>Once you have installed Google Earth, create a main sector directory by right-clicking "My Places" then selecting Add &#9654 Folder. You may name this folder whatever you wish.</p>
        <img class="help" src="img/help1.png">
        <h3 id="artcc">The ARTCC Section</h3>
        <p>The ARTCC Section is used to define major ARTCC/FIR boundaries. Start by creating a subfolder within your main sector directory and name it "ARTCC". Next, use the Add Path tool located on the top toolbar to start defining the ARTCC boundary.</p>
        <img class="help" src="img/help2.png">
        <p>The path must be named with the name of the boundary that the segment is part of, such as KZBW. You may add as many paths as necessary with various names. Names must not contain spaces.</p>
        <img class="help" src="img/help3.png">
        <h3 id="artcc_high">The ARTCC HIGH and ARTCC LOW Sections</h3>
        <p>The ARTCC HIGH and ARTCC LOW Sections are nearly identical to the ARTCC section. However, instead of naming the subfolder "ARTCC", name it "ARTCC HIGH" or "ARTCC LOW", respectively. Paths must be named with the name of the sector or TRACON that the segment is part of, such as CON37 or A90.</p>
        <img class="help" src="img/help4.png">
        <h3 id="sid">The SID and STAR Sections</h3>
        <p>The SID and STAR sections are used to define diagrams and video maps. Their subfolders must be named "SID" or "STAR", respectively. Each subfolder must contain additional folders, one for each diagram that can be toggled on or off by controllers. You may create as many paths as necessary within each diagram subfolder. Paths may remain unnamed or can be named with their VRC color. You may also enter a default color name on the website under "Default Map Color" to rename all unnamed lines. This will require some manual editing of the sector file after its creation. For more information on defining colors, see the "Color Definitions" section on <a target="_blank" href="http://www1.metacraft.com/VRC/docs/doc.php?page=appendix_g">this</a> page.</p>
        <img class="help" src="img/help5.png">
        <h3 id="geo">The GEO section</h3>
        <p>The GEO section is used to define geographic data such as coastlines or major bodies of water. Its subfolder must be named "GEO". You may create as many paths as necessary within the GEO subfolder. Paths may remain unnamed or can be named with their VRC color. You may also enter a default color name on the website under "Default Map Color" to rename all unnamed lines.</p>
        <h3 id="regions">The REGIONS section</h3>
        <p>The REGIONS section is used to define areas filled with a solid color. Its subfolder must be named "REGIONS". Use the Add Polygon tool on the top toolbar to add as many polygons as necessary within the REGIONS folder.</p>
        <img class="help" src="img/help6.png">
        <p>Polygons must be named with their VRC color.</p>
        <img class="help" src="img/help7.png">
        <h3 id="labels">The LABELS section</h3>
        <p>The LABELS section is used to define simple static text strings drawn at specified locations. Its subfolder must be named "LABELS". Use the Add Placemark tool on the top toolbar to add as many placemarks as necessary within the LABELS folder.</p>
        <img class="help" src="img/help8.png">
        <p>Labels must be named with their label name, which may contain spaces. To provide a color for the labels, enter it into the "Default Label Color" box on the website.</p>
        <img class="help" src="img/help9.png">
        <h3 id="uploading_ge_data">Uploading Google Earth Data</h3>
        <p>When you have completed as many sections as necessary on Google Earth, right-click your root sector folder and select Copy.</p>
        <img class="help" src="img/help10.png">
        <p>Next, paste the entire folder inside the text area. Note that large folders may take a few seconds to upload. Wait until the text appears before proceeding.</p>
        <img class="help" src="img/help11.png">
        <!--
        <h3 id="navigraph">Navigraph NavData</h3>
        <p>This tool utilizes data uploaded from Navigraph to generate airport and NAVAID data. Navigraph is a paid subscription service that can be purchased <a target="_blank" href="https://navigraph.com/home">here</a>. To begin, open the Navigraph FMS Data Manager and select Addon Mappings. Next, create a custom addon mapping for X-Plane 11 (NOT X-Plane 11.50 and above). Select a custom install directory of a convenient folder. Note you do not have to own or have X-Plane installed.</p>
        <img class="help" src="img/help12.png">
        <p>Return to the website and enter the coordinate for the center of your sector and define a radius for the extent of data to include. Finally, upload Airports.txt, Navaids.txt, Waypoints.txt and/or ATS.txt from your Navigraph install location defined in the previous step.</p>
        <br>
        <p>When have uploaded all the data for your sector file, click "Create Sector File" to create and download your file.</p>
        -->
        <h2 id="convert">Converting a .sct2 File to a .kml File:</h2>
        <p>To convert an existing .sct2 file to a .kml file to for easier editing either paste the contents of the .sct2 file you wish to convert into the text area or upload the .sct2 file in its entirety. Then click "Convert to KML file". Note that this tool is unable to convert diagrams that use NAVAIDs or fixes to define lines. All lines must be formatted with coordinates only.</p>
        <img class="help" src="img/help13.png">
        <h2 id="mva">Converting FAA MVA/MIA Files:</h2>
        <p>The FAA publishes their MVA and MIA files <a href="https://www.faa.gov/air_traffic/flight_info/aeronav/digital_products/mva_mia/" target="_blank">here</a> on their website. To convert to a .kml file for use with Google Earth, save the .xml version of the file you wish to convert from the FAA website. Depending on your browser, you may need to right click on the link and select "Save link as..." to download.</p>
        <img class="help" src="img/help14.png">
        <p>Once you have downloaded the .xml file, you can upload it to this website to begin the conversion process. This will create a .kml file you can open and edit in Google Earth. To export from Google Earth into a .sct2 format, please see <a href="#uploading_ge_data">Uploading Google Earth Data</a>.</p>
        <img class="help" src="img/help15.png">
        <h2 id="report">Reporting Issues or Suggestions:</h2>
        <p>This project is open source and available on <a target="_blank" href="https://github.com/nrankin18/FETools">GitHub</a>. Please forward issues or suggestions to nathanr (at) bvartcc.com</p>
    </div>

</html>