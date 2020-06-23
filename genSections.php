<?php

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

function formatCoord($decLat, $decLong) {
    $latPre = "N";
    $longPre = "E";

    if ($decLat < 0) {
        $latPre = 'S';
        $decLat = abs($decLat);
    }

    if ($decLong < 0) {
        $longPre = 'W';
        $decLong = abs($decLong);
    }
    return $latPre.DDtoDMS($decLat).' '.$longPre.DDtoDMS($decLong);
}

function DDtoDMS($dec)
{
    $deg = floor($dec);
    $tmpmin = ($dec-$deg)*60;
    $min = floor($tmpmin);
    $tmpsec = ($tmpmin-$min)*60;
    $sec = floor($tmpsec);

    $secdeg = substr($tmpsec-$sec,2,3);

    $deg = sprintf('%03d', $deg);
    $min = sprintf('%02d', $min);
    $sec = sprintf('%02d', $sec);
    $secdeg = sprintf('%03d', $secdeg);

    return $deg.'.'.$min.'.'.$sec.'.'.$secdeg;
}