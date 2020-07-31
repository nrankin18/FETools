<?php
require "genSections.php";
$year = date('Y');
$date = date("m/d/Y");

$name = $_POST['name'];
$callsign = $_POST['callsign'];
$airport = $_POST['airport'];
$lat = $_POST['lat'];
$long = $_POST['long'];
$nmLat = $_POST['nmLat'];
$nmLong = $_POST['nmLong'];
$mv = $_POST['mv'];
$scale = $_POST['scale'];

$rootKML = NULL;

if ($_POST['rootKML'])
    $rootKML = new SimpleXMLElement($_POST['rootKML']);

$latCenter = $_POST['latCenter'];
$longCenter = $_POST['longCenter'];
$range = $_POST['range'];

$airports = NULL;
$navaids = NULL;
$waypoints = NULL;
$airways = NULL;

if ($_POST['airports'])
    $airports = $_POST['airports'];

if ($_POST['navaids'])
    $navaids = $_POST['navaids'];

if ($_POST['waypoints'])
    $waypoints = $_POST['waypoints'];

if ($_POST['airways'])
    $airways = $_POST['airways'];

echo "; $name
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

echo "[INFO]\n$name\n$callsign\n$airport\n$lat\n$long\n$nmLat\n$nmLong\n$mv\n$scale\n";

if ($rootKML) {
    $kml = $rootKML->Folder->Folder;
    
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
                genMap($section, 0);
                break;
            case "STAR":
                echo "\n[STAR]\n";
                genMap($section);
                break;
            case "GEO":
                echo "\n[GEO]\n";
                genMap($section, true);
                break;
            case "REGIONS":
                echo "\n[REGIONS]\n";
                genRegions($section);
                break;
            case "LABELS":
                echo "\n[LABELS]\n";
                genLabels($section);
                break;
        }
    }

}

if ($waypoints) {
    echo "\n[FIXES]\n";
    genWaypoints($waypoints, $latCenter, $longCenter, $range);
}