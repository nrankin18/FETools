<?php
require_once "functions.php";
$year = date('Y');
$date = date("m/d/Y");

$infoName = $_POST['infoName'];
$infoCallsign = $_POST['infoCallsign'];
$infoAirport = $_POST['infoAirport'];
$infoLat="";
if ($_POST['infoLat'])
    $infoLat = DDLatToDMS($_POST['infoLat']);
$infoLong="";
    if ($_POST['infoLong'])
        $infoLong = DDLatToDMS($_POST['infoLong']);
$infoNMLat = $_POST['infoNMLat'];
$infoNMLong = $_POST['infoNMLong'];
$infoMV = $_POST['infoMV'];
$infoScale = $_POST['infoScale'];

$kml = NULL;

if ($_POST['kmlText'])
    $kml = new SimpleXMLElement($_POST['kmlText']);

$sectorColor = $_POST['sectorColor'];
$labelColor = $_POST['labelColor'];


// $navLatCenter = $_POST['navLatCenter'];
// $navLongCenter = $_POST['navLongCenter'];
// $navRadius = $_POST['navRadius'];

$navAirports = NULL;
$navNavaids = NULL;
$navWaypoints = NULL;
$navATS = NULL;

// if ($_POST['navAirports'])
//     $navAirports = $_POST['navAirports'];

// if ($_POST['navNavaids'])
//     $navNavaids = $_POST['navNavaids'];

// if ($_POST['navWaypoints'])
//     $navWaypoints = $_POST['navWaypoints'];

// if ($_POST['navATS'])
//     $navATS = $_POST['navATS'];

echo "; $infoName
; Copyright $year. All rights reserved. 
; This file is intended for the sole use of the members of the VATSIM Online
; Flight Simulation community. This file may
; not be sold, transferred or distributed in any manner.
;
;For use in ASRC, the filename extension will need to be
;changed to .sct. ASRC will NOT show some of the advanced features
;included in this file such as shaded regions and entity labels.


;CHANGE LOG:
;$date - v1.0 - XX - File created
;===============================================================================

";

echo "[INFO]\n$infoName\n$infoCallsign\n$infoAirport\n$infoLat\n$infoLong\n$infoNMLat\n$infoNMLong\n$infoMV\n$infoScale\n";

if ($kml) {
    $kml = $kml->Folder->Folder;

    foreach ($kml as $section) {
        switch ($section->name) {
            case "ARTCC":
                echo "\n[ARTCC]\n";
                genARTCC($section);
                break;
            case "ARTCC HIGH":
                echo "\n[ARTCC HIGH]\n";
                genARTCC($section);
                break;

            case "ARTCC LOW":
                echo "\n[ARTCC LOW]\n";
                genARTCC($section);
                break;
            case "SID":
                echo "\n[SID]\n";
                genMap($section, $sectorColor, false);
                break;
            case "STAR":
                echo "\n[STAR]\n";
                genMap($section, $sectorColor, false);
                break;
            case "GEO":
                echo "\n[GEO]\n";
                genMap($section, $sectorColor, true);
                break;
            case "REGIONS":
                echo "\n[REGIONS]\n";
                genRegions($section);
                break;
            case "LABELS":
                echo "\n[LABELS]\n";
                genLabels($section, $labelColor);
                break;
        }
    }
}

if ($navWaypoints) {
    echo "\n[FIXES]\n";
    genWaypoints($navWaypoints, $navLatCenter, $navLongCenter, $navRadius);
}

if ($navNavaids) {
    genERAMNavaids($navNavaids, $navLatCenter, $navLongCenter, $navRadius);
}

if ($navAirports) {
    genAirports($navAirports, $navLatCenter, $navLongCenter, $navRadius);
}

if ($navATS) {
    genAirways($navATS, $navLatCenter, $navLongCenter, $navRadius);
}

function genARTCC($section)
{
    $lines = $section->Placemark;
    foreach ($lines as $line) {
        $coords = $line->LineString->coordinates;
        $coords = explode(" ", $coords);
        $name = $line->name;
        for ($i = 0; $i < count($coords) - 2; $i++) {
            $coord1 = explode(",", $coords[$i]);
            $coord2 = explode(",", $coords[$i + 1]);
            if ($name)
                echo $name;
            else
                echo "ARTCC";
            echo " " . DDToDMS($coord1[1], $coord1[0]) . " " . DDToDMS($coord2[1], $coord2[0]) . "\n";
        }
    }
}

function genMap($section, $defaultColor, $isGeo)
{
    if (!$isGeo) {
        $diagrams = $section->Folder;
        foreach ($diagrams as $diagram) {
            echo substr(str_pad($diagram->name, 26), 0, 26);
            echo "N000.00.00.000 W000.00.00.000 N000.00.00.000 W000.00.00.000 ;Removes Label\n\n";
            evaluateLines($diagram, $defaultColor);
        }
    } else {
        evaluateLines($section, $defaultColor);
    }
    echo "\n";
}

function genRegions($regions)
{
    $regions = $regions->Placemark;
    foreach ($regions as $region) {
        echo $region->name;

        foreach (explode(" ", $region->Polygon->outerBoundaryIs->LinearRing->coordinates) as $coordinate) {
            $parts = explode(",", $coordinate);
            if (sizeof($parts) == 3) {
                echo " " . DDToDMS($parts[1], $parts[0]) . "\n";
            }
        }
        echo "\n";
    }
}

function genLabels($labels, $defaultColor)
{
    $labels = $labels->Placemark;
    foreach ($labels as $label) {
        echo "\"" . $label->name . "\"";
        $coords = $label->Point->coordinates;
        $parts = explode(",", $coords);
        echo " " . DDToDMS($parts[1], $parts[0]) . " " . $defaultColor . "\n";
    }
}

function genWaypoints($waypoints, $latCenter, $longCenter, $range)
{
    $waypoints = explode("\n", $waypoints);
    foreach ($waypoints as $waypoint) {
        $waypoint = explode(",", $waypoint);
        if (count($waypoint) >= 3)
            if (distance($waypoint[1], $waypoint[2], $latCenter, $longCenter) < NMtoMeters($range))
                echo $waypoint[0] . " " . DDToDMS($waypoint[1], $waypoint[2]) . "\n";
    }
}

function genNavaids($navaids, $latCenter, $longCenter, $range)
{
    $vors = [];
    $ndbs = [];
    $navaids = explode("\n", $navaids);
    foreach ($navaids as $navaid) {
        $navaid = explode(",", $navaid);
        if (count($navaid) >= 3)
            if (distance($navaid[6], $navaid[7], $latCenter, $longCenter) < NMtoMeters($range)) {
                if (isVORFreq($navaid[2]))
                    array_push($vors, new NAVAID($navaid[0], $navaid[1], $navaid[2], $navaid[6], $navaid[7]));
                else if (isNDBFreq($navaid[2])) {
                    array_push($ndbs, new NAVAID($navaid[0], $navaid[1], $navaid[2], $navaid[6], $navaid[7]));
                }
            }
    }

    echo "\n[VOR]\n";
    foreach ($vors as $vor) {
        echo "$vor\n";
    }

    echo "\n[NDB]\n";
    foreach ($ndbs as $ndb) {
        echo "$ndb\n";
    }
}

function genERAMNavaids($navaids, $latCenter, $longCenter, $range)
{
    $vors = [];
    $ndbs = [];
    $navaids = explode("\n", $navaids);
    foreach ($navaids as $navaid) {
        $navaid = explode(",", $navaid);
        if (count($navaid) >= 3)
            if (distance($navaid[6], $navaid[7], $latCenter, $longCenter) < NMtoMeters($range)) {
                if (isVORFreq($navaid[2]))
                    array_push($vors, new eramNAVAID($navaid[6], $navaid[7]));
                else if (isNDBFreq($navaid[2])) {
                    array_push($ndbs, new eramNAVAID($navaid[6], $navaid[7]));
                }
            }
    }

    echo "\n[VOR]\n";
    foreach ($vors as $vor) {
        echo "$vor\n";
    }

    echo "\n[NDB]\n";
    foreach ($ndbs as $ndb) {
        echo "$ndb\n";
    }
}


class NAVAID
{
    public $id;
    public $name;
    public $freq;
    public $lat;
    public $long;

    function __construct($id, $name, $freq, $lat, $long)
    {
        $this->id = $id;
        $this->name = $name;
        $this->freq = $freq;
        $this->lat = $lat;
        $this->long = $long;
    }

    function __toString()
    {
        return $this->id . " " . $this->freq . " " . DDtoDMS($this->lat, $this->long) . " ;" . $this->name;
    }
}

class eramNAVAID
{
    public $lat;
    public $long;

    function __construct($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    function __toString()
    {
        return "<Element xsi:type=\"Symbol\" Filters=\"\" Lat=\"$this->lat\" Lon=\"$this->long\" />";
    }
}

function isVORFreq($freq)
{
    if ($freq < 108 || $freq > 117.95)
        return false;
    if ($freq >= 108.0 && $freq <= 111.95) {
        $freq = (string) $freq;
        $digit = (int) $freq[4];
        if ($digit % 2 != 0)
            return false;
    }
    return true;
}

function isNDBFreq($freq)
{
    if ($freq >= 190 && $freq <= 1750)
        return ($freq < 960 || $freq > 1215);  //removes TACANs
}

function genAirports($airports, $latCenter, $longCenter, $range)
{
    $airportObjects = [];
    $airports = explode("\n\r\n", $airports);
    foreach ($airports as $airport) {
        $airport = explode("\n", $airport);
        $airportDetails = explode(",", $airport[0]);
        if ($airportDetails[0] != "X")
            if (distance($airportDetails[3], $airportDetails[4], $latCenter, $longCenter) < NMtoMeters($range)) {
                $rwyObjects = [];
                $i = 1;
                while (isset($airport[$i])) {
                    $rwyDetails = explode(',', $airport[$i]);
                    $j = $i + 1;

                    $endLat = NULL;
                    $endLong = NULL;
                    while (isset($airport[$j])) {
                        $oppRwyDetails = explode(',', $airport[$j]);
                        if ($oppRwyDetails[1] == oppRwyID($rwyDetails[1])) {
                            $endLat = $oppRwyDetails[8];
                            $endLong = $oppRwyDetails[9];
                            unset($airport[$j]);
                            $airport = array_values($airport);
                            break;
                        }
                        $j++;
                    }
                    array_push($rwyObjects, new Runway($rwyDetails[1], $rwyDetails[2], $rwyDetails[8], $rwyDetails[9], $endLat, $endLong));
                    $i++;
                }
                array_push($airportObjects, new Airport($airportDetails[1], $airportDetails[2], $airportDetails[3], $airportDetails[4], $rwyObjects));
            }
    }

    echo "\n[AIRPORT]\n";
    foreach ($airportObjects as $airport) {
        echo "$airport\n";
    }

    echo "\n[RUNWAY]\n";
    foreach ($airportObjects as $airport) {
        echo ";$airport->id\n";
        foreach ($airport->rwys as $rwy)
            echo "$rwy\n";
    }
}

class Airport
{
    public $id;
    public $name;
    public $lat;
    public $long;
    public $rwys;

    function __construct($id, $name, $lat, $long, $rwys)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lat = $lat;
        $this->long = $long;
        $this->rwys = $rwys;
    }

    function __toString()
    {
        return $this->id . " 122.800 " . DDtoDMS($this->lat, $this->long) . " E ;" . $this->name;
    }
}

class Runway
{
    public $startID;
    public $endID;

    public $startMag;
    public $endMag;

    public $startLat;
    public $startLong;

    public $endLat;
    public $endLong;

    function __construct($id, $mag, $startLat, $startLong, $endLat, $endLong)
    {
        $this->startID = $id;
        $this->endID = oppRwyID($id);

        $this->startMag = $mag;
        $mag += 180;
        if ($mag > 360)
            $mag -= 360;
        $this->endMag = $mag;

        $this->startLat = $startLat;
        $this->startLong = $startLong;

        $this->endLat = $endLat;
        $this->endLong = $endLong;
    }

    function __toString()
    {
        return $this->startID . " " . $this->endID . " " . $this->startMag . " " . $this->endMag . " " . DDtoDMS($this->startLat, $this->startLong) . " " . DDtoDMS($this->endLat, $this->endLong);
    }
}

function oppRwyID($rwy)
{
    $num = (int) substr($rwy, 0, 2);
    $num += 18;
    if ($num > 36)
        $num -= 36;

    if (strlen($rwy) == 3)
        switch ($rwy[2]) {
            case 'C':
                return $num . 'C';
            case 'R':
                return $num . 'L';
            case 'L':
                return $num . 'R';
        }
    return $num;
}

function genAirways($airways, $latCenter, $longCenter, $range)
{
    $lowAirways = [];
    $highAirways = [];
    $airways = explode("\n\r\n", $airways);
    foreach ($airways as $airway) {
        $airway = explode("\n", $airway);
        $airwayName = "";
        foreach ($airway as $waypoint) {
            $waypointDetails = explode(",", $waypoint);
            if (count($waypointDetails) > 1) {
                if (count($waypointDetails) < 4) {
                    $airwayName = $waypointDetails[1];
                } else if ((distance($waypointDetails[2], $waypointDetails[3], $latCenter, $longCenter) < NMtoMeters($range)) || (distance($waypointDetails[5], $waypointDetails[6], $latCenter, $longCenter) < NMtoMeters($range))) {
                    if ($airwayName[0] == "V" || $airwayName[0] == "T") {
                        array_push($lowAirways, new Airway($airwayName, $waypointDetails[2], $waypointDetails[3], $waypointDetails[5], $waypointDetails[6]));
                    } else if ($airwayName[0] == "J" || $airwayName[0] == "Q") {
                        array_push($highAirways, new Airway($airwayName, $waypointDetails[2], $waypointDetails[3], $waypointDetails[5], $waypointDetails[6]));
                    }
                }
            }
        }
    }

    echo "\n[LOW AIRWAY]\n";
    foreach ($lowAirways as $airway) {
        echo "$airway\n";
    }

    echo "\n[HIGH AIRWAY]\n";
    foreach ($highAirways as $airway) {
        echo "$airway\n";
    }
}

class Airway
{
    public $id;

    public $startLat;
    public $startLong;

    public $endLat;
    public $endLong;

    function __construct($id, $startLat, $startLong, $endLat, $endLong)
    {
        $this->id = $id;

        $this->startLat = $startLat;
        $this->startLong = $startLong;

        $this->endLat = $endLat;
        $this->endLong = $endLong;
    }

    function __toString()
    {
        return $this->id . " " . DDtoDMS($this->startLat, $this->startLong) . " " . DDtoDMS($this->endLat, $this->endLong);
    }
}
