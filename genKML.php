<?php
$sector = $_POST['sector'];

$dom = new DOMDocument();
$dom->encoding = 'utf-8';
$dom->xmlVersion = '1.0';
$dom->formatOutput = true;
$kml = $dom->createElement('kml');
$dom->appendChild($kml);
$document = $dom->createElement('Document');
$kml->appendChild($document);

$lines = explode("\n", $sector);
$cleanLines = [""];
$j = 0;


$dom->save("sector.kml");

for ($i = 0; $i < sizeof($lines); $i++) {
    $line = $lines[$i];
    $line = preg_replace('!;.*!s', '', $line);
    $line = preg_replace('!\s+!', ' ', $line);
    $line = trim($line);
    if (!empty($line)) {
        $cleanLines[$j]=$line;
        $j++;
    }
}

$lines = $cleanLines;

for ($i = 0; $i < sizeof($lines); $i++) {
    $line = $lines[$i];
    $elements = preg_split('/("[^"]*")|\h+/', $line, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

    // Determine state
    if ($elements[0]=="[REGIONS]") {
        $state = "regions";
        continue;
    } else if ($elements[0]=="[LABELS]") {
        $state = "labels";
        continue;
    }

    // Switch on states

    // Regions
    if ($state == "regions") {

        $placemark = $dom->createElement('Placemark');
        $document->appendChild($placemark);
        $name = $dom->createElement('name', $elements[0]);
        $placemark->appendChild($name);
        $poly = $dom->createElement('Polygon');
        $placemark->appendChild($poly);
        $outerBoundary = $dom->createElement('outerBoundaryIs');
        $poly->appendChild($outerBoundary);
        $ring = $dom->createElement('LinearRing');
        $outerBoundary->appendChild($ring);

        $coordinateStr = DMStoDec($elements[1], $elements[2]);

        while ($lines[$i+1]) {
            $key = explode(" ", $lines[$i+1])[0];
            if (!preg_match("/(N|S)\d{1,3}.\d{1,2}.\d{1,2}.\d{1,3}/", $key)) {
                break;
            }

            $i=$i+1;
            $line = $lines[$i];
            $elements = explode(" ", $line);
            $coordinateStr = $coordinateStr." ".DMStoDec($elements[0], $elements[1]);
        }

        $coords = $dom->createElement('coordinates', $coordinateStr);
        $ring->appendChild($coords);
    } else if ($state = "labels") {
        $placemark = $dom->createElement('Placemark');
        $document->appendChild($placemark);
        $name = $dom->createElement('name', str_replace('"',"",$elements[0]));
        $placemark->appendChild($name);
        $point = $dom->createElement('Point');
        $placemark->appendChild($point);
        $coordinates = $dom->createElement('coordinates', DMStoDec($elements[1], $elements[2]));
        $point->appendChild($coordinates); 
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

$dom->save("sector.kml");