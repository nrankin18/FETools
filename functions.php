<?php

// Formats coordinates from decimal (xx.xxxx..., xx.xxxx) to degrees, minutes and seconds with a prefix (N/Sdd.mm.ss.sss E/Wdd.mm.ss.sss)
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


// Formats coordinates from decimal (xx.xxxx...) to degrees, minutes and seconds (dd.mm.ss.sss). Do not pass negative values
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

// Formats line as N000.00.00.000 W000.00.00.000 N000.00.00.000 W000.00.00.000
function formatLine($Placemark)
{
    $coords = $Placemark->LineString->coordinates;
    $coords = explode(" ", $coords);
    $coord1 = explode(",", $coords[0]);
    $coord2 = explode(",", $coords[1]);
    echo formatCoord($coord1[1], $coord1[0]) . " " . formatCoord($coord2[1], $coord2[0]);
}


function evaluateLines($region, $defaultColor) {
    $lines = $region->Placemark;
    foreach ($lines as $line) {
        $coords = $line->LineString->coordinates;
        $coords = explode(" ", $coords);
        for ($i=0; $i<count($coords)-2; $i++) {
            $coord1 = explode(",", $coords[$i]);
            $coord2 = explode(",", $coords[$i+1]);
            echo "                          ".formatCoord($coord1[1],$coord1[0])." ";
            echo formatCoord($coord2[1],$coord2[0]);
            if (isset($line->name)) {
                echo " ".$line->name;
            } else if (!empty($defaultColor)) {
                echo " ".$defaultColor;
            }

            //Inline comment
            if (isset($line->description)) {
                echo " ;".$line->description;
            }
            echo "\n";
        }
    }
}

function DMStoDec($lat, $long) {
    $lat = explode(".", $lat);
    $decLat = (0.000277777778)*($lat[2].".".$lat[3]);
    $decLat += 0.01666667*$lat[1];
    if (strpos($lat[0], 'N') !== false) {
        $decLat += floatval(ltrim($lat[0], 'N'));
    } else {
        $decLat += floatval(ltrim($lat[0], 'S'));
        $decLat *= -1;
    }

    $long = explode(".", $long);
    $decLong = (0.000277777778)*($long[2].".".$long[3]);
    $decLong += 0.01666667*$long[1];
    if (strpos($long[0], 'E') !== false) {
        $decLong += floatval(ltrim($long[0], 'E'));
    } else {
        $decLong += floatval(ltrim($long[0], 'W'));
        $decLong *= -1;
    }

    return $decLong.",".$decLat.",0";
}