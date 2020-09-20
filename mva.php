<?php
require "functions.php";

$xml = new DOMDocument();
$xml->loadXML($_POST['xml']);
$xml->save("test.xml");

$dom = new DOMDocument();
$dom->encoding = 'utf-8';
$dom->xmlVersion = '1.0';
$dom->formatOutput = true;
$kml = $dom->createElement('kml');
$dom->appendChild($kml);
$document = $dom->createElement('Document');
$kml->appendChild($document);

$folderSID = $dom->createElement('Folder');
$name = $dom->createElement('name', "SID");
$folderSID->appendChild($name);
$document->appendChild($folderSID);

$folderMVA = $dom->createElement('Folder');
$name = $dom->createElement('name', "MVA/MIA");
$folderMVA->appendChild($name);
$folderSID->appendChild($folderMVA);

foreach ($xml->getElementsByTagName('hasMember') as $line) {
    foreach ($line->getElementsByTagName('exterior') as $xmlcoords) {
        $placemark = $dom->createElement('Placemark');
        $lineString = $dom->createElement('LineString');
        $placemark->appendChild($lineString);

        $xmlcoords = $xmlcoords->getElementsByTagName('posList')->item(0)->nodeValue;
        $xmlcoords = explode(" ", $xmlcoords);

        $coordinateStr = "";
        for ($i = 0; $i < count($xmlcoords); $i += 2) {
            $coordinateStr = $coordinateStr . $xmlcoords[$i] . ',' . $xmlcoords[$i + 1] . ',0 ';
        }

        $coordinates = $dom->createElement('coordinates', $coordinateStr);
        $lineString->appendChild($coordinates);
        $folderMVA->appendChild($placemark);
    }
}

$dom->save("sector.kml");
