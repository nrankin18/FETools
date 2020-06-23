<?php
require "genSections.php";
$year = date('Y');
$date = date("m/d/Y");

if ($_POST['rootKML']) {
    $rootKML = new SimpleXMLElement($_POST['rootKML']);
}

echo "; Copyright $year. All rights reserved. 
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

if ($rootKML) {
    $kml = $rootKML->Document->Folder->Folder;
    
    foreach ($kml as $section) {
        switch ($section->name) {
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