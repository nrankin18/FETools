<?php

// Formats coordinate from decimal (xx.xxxx..., xx.xxxx) to degrees, minutes and seconds with a prefix (N/Sdd.mm.ss.sss E/Wdd.mm.ss.sss)
function DDToDMS($decLat, $decLong) {
    return DDLatToDMS($decLat)." ".DDLongToDMS($decLong);
}


function DDLatToDMS ($decLat) {
    $latPre = "N";
    if ($decLat < 0) {
        $latPre = 'S';
        $decLat = abs($decLat);
    }
    return $latPre.decToDMS($decLat);
}


function DDLongToDMS ($decLong) {
    $longPre = "E";
    if ($decLong < 0) {
        $longPre = 'W';
        $decLong = abs($decLong);
    }
    return $longPre.decToDMS($decLong);
}

// Formats coordinates from decimal (xx.xxxx...) to degrees, minutes and seconds (dd.mm.ss.sss). Do not pass negative values. Helper function
function decToDMS($dec) {
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

/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);
  
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;
  
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
  }

  function NMtoMeters ($nm) {
    return $nm * 1852;
  }