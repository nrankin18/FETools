<?php
require_once "functions.php";
$year = date('Y');
$date = date("m/d/Y");

$infoName = $_POST['infoName'];
$infoCallsign = $_POST['infoCallsign'];
$infoAirport = $_POST['infoAirport'];
$infoLat = "";
if ($_POST['infoLat'])
    $infoLat = DDLatToDMS($_POST['infoLat']);
$infoLong = "";
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


$navLatCenter = $_POST['navLatCenter'];
$navLongCenter = $_POST['navLongCenter'];
$navRadius = $_POST['navRadius'];

$useNav = false;

if (!empty($navLatCenter) && !empty($navLongCenter) && !empty($navRadius))
    $useNav = true;

$navExpDate = "";
if ($useNav) {
    $navExpDate = file_get_contents('NASR/date.txt');
}

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

if ($useNav) {
    //genAirports($navLatCenter, $navLongCenter, $navRadius);
    //genNavaids($navLatCenter, $navLongCenter, $navRadius);
    //genFixes($navLatCenter, $navLongCenter, $navRadius);
    genAirway($navLatCenter, $navLongCenter, $navRadius);
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

function genFixes($latCenter, $longCenter, $range)
{
    $navFIX = file_get_contents('NASR/FIX.txt');
    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[FIXES]\n");
    $fixes = explode("\n", $navFIX);
    foreach ($fixes as $fix) {
        if (substr($fix, 0, 4) == "FIX1") {
            $id = trim(substr($fix, 4, 30));
            $lat = trim(substr($fix, 66, 14));
            $long = trim(substr($fix, 80, 14));
            $type = trim(substr($fix, 213, 15));

            if ($type == "WAYPOINT" || $type == "RADAR" || $type == "REP-PT" || $type == "MIL-REP-PT" || $type == "MIL-WAYPOINT") {
                $lat = DMSLattoDec(substr($lat, 0, 2), substr($lat, 3, 2), substr($lat, 6, 6), substr($lat, 12, 1));
                $long = DMSLongtoDec(substr($long, 0, 3), substr($long, 4, 2), substr($long, 7, 2), substr($long, 13, 1));

                if (distance($lat, $long, $latCenter, $longCenter) < NMtoMeters($range)) {
                    echo ($id . " " . DDToDMS($lat, $long) . "\n");
                }
            }
        }
    }
}

function genNavaids($latCenter, $longCenter, $range)
{
    $navNAV = file_get_contents('NASR/NAV.txt');
    $vors = [];
    $ndbs = [];
    $navaids = explode("\n", $navNAV);
    foreach ($navaids as $navaid) {
        if (substr($navaid, 0, 4) == "NAV1") {
            $id = trim(substr($navaid, 28, 4));
            $name = trim(substr($navaid, 42, 30));
            $type = trim(substr($navaid, 8, 20));
            $lat = trim(substr($navaid, 371, 14));
            $long = trim(substr($navaid, 396, 14));
            $freq = trim(substr($navaid, 533, 6));
            $status = trim(substr($navaid, 766, 30));

            if ($status != "DECOMMISSIONED") {
                if ($type == "VORTAC" || $type == "VOR/DME" || $type == "VOR") {
                    $lat = DMSLattoDec(substr($lat, 0, 2), substr($lat, 3, 2), substr($lat, 6, 6), substr($lat, 12, 1));
                    $long = DMSLongtoDec(substr($long, 0, 3), substr($long, 4, 2), substr($long, 7, 2), substr($long, 13, 1));

                    if (distance($lat, $long, $latCenter, $longCenter) < NMtoMeters($range)) {
                        array_push($vors, new NAVAID($id, $name, $freq, $lat, $long));
                    }
                }

                if ($type == "NDB" || $type == "NDB/DME" || $type == "UHF/NDB") {
                    $lat = DMSLattoDec(substr($lat, 0, 2), substr($lat, 3, 2), substr($lat, 6, 6), substr($lat, 12, 1));
                    $long = DMSLongtoDec(substr($long, 0, 3), substr($long, 4, 2), substr($long, 7, 2), substr($long, 13, 1));

                    if (distance($lat, $long, $latCenter, $longCenter) < NMtoMeters($range)) {
                        array_push($ndbs, new NAVAID($id, $name, $freq, $lat, $long));
                    }
                }
            }
        }
    }

    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[VOR]\n");
    foreach ($vors as $vor) {
        echo ($vor . "\n");
    }
    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[NDB]\n");
    foreach ($ndbs as $ndb) {
        echo ($ndb . "\n");
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

function genAirports($latCenter, $longCenter, $range)
{
    $apts = [];

    $navAPT = fopen('NASR/APT.txt', 'r');

    $type = "";
    $id = "";
    $icao = "";
    $name = "";
    $lat = "";
    $long = "";
    $open = "";
    $status = "";
    $ctaf = "";
    $magVar = "";
    $rwys = [];
    while (($airport = fgets($navAPT))) {
        if (substr($airport, 0, 3) == "APT") {
            if (!empty($id)) {
                array_push($apts, new Airport($id, $name, $ctaf, $lat, $long, $rwys));
                $id = "";
            }
            $type = trim(substr($airport, 14, 13));
            $id = trim(substr($airport, 27, 4));
            $icao = trim(substr($airport, 1210, 7));
            $name = trim(substr($airport, 133, 50));
            $lat = trim(substr($airport, 523, 15));
            $long = trim(substr($airport, 550, 15));
            $open = trim(substr($airport, 185, 2));
            $status = trim(substr($airport, 840, 2));
            $ctaf = trim(substr($airport, 988, 7));
            $magVar = trim(substr($airport, 586, 3));
            $rwys = [];

            if ($type == "AIRPORT" && $open == "PU" && $status == "O") { // PU (public) O (operational)
                $lat = DMSLattoDec(substr($lat, 0, 2), substr($lat, 3, 2), substr($lat, 6, 6), substr($lat, 13, 1));
                $long = DMSLongtoDec(substr($long, 0, 3), substr($long, 4, 2), substr($long, 7, 2), substr($long, 13, 1));

                if (distance($lat, $long, $latCenter, $longCenter) < NMtoMeters($range)) {
                    if (!empty($icao)) {
                        $id = $icao;
                    } else if (strlen($id) == 3) {
                        $id = "K" . $id;
                    }

                    if (empty($ctaf)) {
                        $ctaf = "122.800";
                    }
                } else {
                    $id = "";
                }
            } else {
                $id = "";
            }
        } else if (substr($airport, 0, 3) == "RWY" && !empty($id)) {
            $startLat = "";
            $startLong = "";

            $endLat = "";
            $endLong = "";

            $startID = trim(substr($airport, 65, 3));
            $startTru = trim(substr($airport, 68, 3));
            $startLat = trim(substr($airport, 88, 15));
            $startLong = trim(substr($airport, 115, 15));

            $endID = trim(substr($airport, 287, 3));
            $endTru = trim(substr($airport, 290, 3));
            $endLat = trim(substr($airport, 310, 15));
            $endLong = trim(substr($airport, 337, 15));

            if (empty($startLat) || empty($startLong) || empty($endLat) || empty($endLong)) {
                continue;
            }

            $startLat = DMSLattoDec(substr($startLat, 0, 2), substr($startLat, 3, 2), substr($startLat, 6, 6), substr($startLat, 13, 1));
            $startLong = DMSLongtoDec(substr($startLong, 0, 3), substr($startLong, 4, 2), substr($startLong, 7, 2), substr($startLong, 13, 1));

            $endLat = DMSLattoDec(substr($endLat, 0, 2), substr($endLat, 3, 2), substr($endLat, 6, 6), substr($endLat, 13, 1));
            $endLong = DMSLongtoDec(substr($endLong, 0, 3), substr($endLong, 4, 2), substr($endLong, 7, 2), substr($endLong, 13, 1));

            if (!empty($startTru) && !empty($magVar)) {
                $startMag = truToMag($startTru, $magVar);
            } else {
                $startMag = $startID . "0";
            }

            if (!empty($endTru) && !empty($magVar)) {
                $endMag = truToMag($endTru, $magVar);
            } else {
                $endMag = $endID . "0";
            }

            array_push($rwys, new Runway($startID, $endID, $startMag, $endMag, $startLat, $startLong, $endLat, $endLong));
        }
    }

    if (!empty($id)) {
        array_push($apts, new Airport($id, $name, $ctaf, $lat, $long, null));
    }

    fclose($navAPT);

    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[AIRPORT]\n");
    foreach ($apts as $apt) {
        echo ($apt . "\n");
    }

    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[RUNWAY]\n");
    foreach ($apts as $apt) {
        if ($apt->rwys) {
            echo (";" . $apt->id . "\n");
        }
        $runways = $apt->rwys;
        foreach ($runways as $rwyObj) {
            echo ($rwyObj . "\n");
        }
    }
}

class Airport
{
    public $id;
    public $name;
    public $lat;
    public $long;
    public $rwys;
    public $ctaf;

    function __construct($id, $name, $ctaf, $lat, $long, $rwys)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ctaf = $ctaf;
        $this->lat = $lat;
        $this->long = $long;
        $this->rwys = $rwys;
    }

    function __toString()
    {
        return $this->id . " " . $this->ctaf . " " . DDtoDMS($this->lat, $this->long) . " E ;" . $this->name;
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

    function __construct($startID, $endID, $startMag, $endMag, $startLat, $startLong, $endLat, $endLong)
    {
        $this->startID = $startID;
        $this->endID = $endID;

        $this->startMag = $startMag;
        $this->endMag = $endMag;

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

function genAirway($latCenter, $longCenter, $range)
{
    $navAWY = fopen('NASR/AWY.txt', 'r');

    $lowAwys = [];
    $highAwys = [];

    $id = "";
    $startLat = "";
    $startLong = "";
    $endLat = "";
    $endLong = "";
    while (($airway = fgets($navAWY))) {
        if (substr($airway, 0, 4) == "AWY2") {

            if (empty($id) || $id != trim(substr($airway, 4, 5))) {
                if (empty(trim(substr($airway, 83, 14))) || empty(trim(substr($airway, 97, 14)))) {
                    continue;
                }
                $id = trim(substr($airway, 4, 5));
                $startLat = trim(substr($airway, 83, 14));
                $startLat = DMSLattoDec(substr($startLat, 0, 2), substr($startLat, 3, 2), substr($startLat, 6, -1), substr($startLat, -1));
                $startLong = trim(substr($airway, 97, 14));
                $startLong = DMSLongtoDec(substr($startLong, 0, 3), substr($startLong, 4, 2), substr($startLong, 7, -1), substr($startLong, -1));
            } else if ($id == trim(substr($airway, 4, 5))) {
                if (empty(trim(substr($airway, 83, 14))) || empty(trim(substr($airway, 97, 14)))) {
                    continue;
                }
                $endLat = trim(substr($airway, 83, 14));
                $endLat = DMSLattoDec(substr($endLat, 0, 2), substr($endLat, 3, 2), substr($endLat, 6, -1), substr($endLat, -1));
                $endLong = trim(substr($airway, 97, 13));
                $endLong = DMSLongtoDec(substr($endLong, 0, 3), substr($endLong, 4, 2), substr($endLong, 7, -1), substr($endLong, -1));

                if ((distance($startLat, $startLong, $latCenter, $longCenter) < NMtoMeters($range)) || (distance($endLat, $endLong, $latCenter, $longCenter) < NMtoMeters($range))) {
                    if (strpos($id, 'J') !== false || strpos($id, 'Q') !== false) {
                        array_push($highAwys, new Airway($id, $startLat, $startLong, $endLat, $endLong));
                    } else if (strpos($id, 'V') !== false || strpos($id, 'T') !== false) {
                        array_push($lowAwys, new Airway($id, $startLat, $startLong, $endLat, $endLong));
                    }
                }

                $startLat = $endLat;
                $startLong = $endLong;
            }
        }
    }


    fclose($navAWY);

    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[LOW AIRWAY]\n");
    foreach ($lowAwys as $lowAwy) {
        echo ($lowAwy . "\n");
    }

    echo ("\n;Navigation data autogenerated from FAA NASR. Expires " . $GLOBALS['navExpDate'] . ".\n");
    echo ("[HIGH AIRWAY]\n");
    foreach ($highAwys as $highAwy) {
        echo ($highAwy . "\n");
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
