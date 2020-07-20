<?php
require "functions.php";

function genARTCC($section)
{
    $lines= $section->Placemark;
    foreach ($lines as $line) {
        $coords = $line->LineString->coordinates;
        $coords = explode(" ", $coords);
        $name = $line->name;
        for ($i=0; $i<count($coords)-2; $i++) {
            $coord1 = explode(",", $coords[$i]);
            $coord2 = explode(",", $coords[$i+1]);
            if ($name)
                echo $name;
            else
                echo "ARTCC";
            echo " ".formatCoord($coord1[1],$coord1[0])." ";
            echo formatCoord($coord2[1],$coord2[0]);
            echo "\n";
        }
    }
}

function genMap($section, $isGeo)
{
    $defaultColor = ""; // Assign this color from POST to add default

    $sids= $section->Folder;
    foreach ($sids as $sid) {
        echo substr(str_pad($sid->name, 26), 0, 26);
        echo "N000.00.00.000 W000.00.00.000 N000.00.00.000 W000.00.00.000 ;Removes Label\n\n";

        $regions = $sid->Folder;
        foreach ($regions as $region) {
            echo "\n                          ;" . $region->name . "\n";
            evaluateLines($region, $defaultColor);
        }
        evaluateLines($sid, $defaultColor);
        echo "\n";
    }
}

function genRegions($regions) {
    $regions = $regions->Placemark;
    foreach ($regions as $region) {
        echo $region->name;

        foreach (explode(" ", $region->Polygon->outerBoundaryIs->LinearRing->coordinates) as $coordinate) {
            $parts = explode(",", $coordinate);
            if (sizeof($parts) == 3) {
                echo " ".formatCoord($parts[1], $parts[0])."\n";
            }
        }
        echo "\n";
    }
}

function genLabels($labels) {
    $labels = $labels->Folder->Placemark;
    foreach ($labels as $label) {
        echo "\"".$label->name."\"";
        $coords = $label->Point->coordinates;
        $parts = explode(",", $coords);
        echo " ".formatCoord($parts[1], $parts[0])."\n";
    }
}
