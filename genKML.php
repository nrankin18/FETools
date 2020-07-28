<?php
require "functions.php";

$sector = $_POST['sector'];

$dom = new DOMDocument();
$dom->encoding = 'utf-8';
$dom->xmlVersion = '1.0';
$dom->formatOutput = true;
$kml = $dom->createElement('kml');
$dom->appendChild($kml);
$document = $dom->createElement('Document');
$kml->appendChild($document);

$folderARTCC = $dom->createElement('Folder');
$name = $dom->createElement('name', "ARTCC");
$folderARTCC->appendChild($name);
$document->appendChild($folderARTCC);

$folderARTCCLow = $dom->createElement('Folder');
$name = $dom->createElement('name', "ARTCC LOW");
$folderARTCCLow->appendChild($name);
$document->appendChild($folderARTCCLow);

$folderARTCCHigh = $dom->createElement('Folder');
$name = $dom->createElement('name', "ARTCC HIGH");
$folderARTCCHigh->appendChild($name);
$document->appendChild($folderARTCCHigh);

$folderSID = $dom->createElement('Folder');
$name = $dom->createElement('name', "SID");
$folderSID->appendChild($name);
$document->appendChild($folderSID);

$folderSTAR = $dom->createElement('Folder');
$name = $dom->createElement('name', "STAR");
$folderSTAR->appendChild($name);
$document->appendChild($folderSTAR);

$folderGEO = $dom->createElement('Folder');
$name = $dom->createElement('name', "GEO");
$folderGEO->appendChild($name);
$document->appendChild($folderGEO);

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

$state = NULL;
$lines = $cleanLines;

for ($i = 0; $i < sizeof($lines); $i++) {
    $line = $lines[$i];
    $elements = preg_split('/("[^"]*")|\h+/', $line, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

    // Determine state
    if ($elements[0]=="[INFO]" || $elements[0] == "VOR" || $elements[0] == "NDB" || $elements[0] == "FIXES" || $elements[0] == "AIRPORT" || $elements[0] == "RUNWAY" || $elements[0] == "LOW AIRWAY" || $elements[0] == "HIGH AIRWAY" || $elements[0] =="#define" ) {
        $state = "skip ";
        continue;
    } else if ($elements[0]=="[REGIONS]") {
        $state = "regions";
        continue;
    } else if ($elements[0]=="[LABELS]") {
        $state = "labels";
        continue;
    }  else if ($elements[0]=="[ARTCC]") {
        $state = "artcc";
        continue;
    }   else if ($elements[0]=="[ARTCC" && $elements[1] == "HIGH]") {
        $state = "artcc high";
        continue;
    } else if ($elements[0]=="[ARTCC" && $elements[1] == "LOW]") {
        $state = "artcc low";
        continue;
    } else if ($elements[0]=="[SID]") {
        $state = "sid";
        continue;
    } else if ($elements[0]=="[STAR]") {
        $state = "star";
        continue;
    } else if ($elements[0]=="[GEO]") {
        $state = "geo";
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
    } else if ($state == "labels") {
        $placemark = $dom->createElement('Placemark');
        $document->appendChild($placemark);
        $name = $dom->createElement('name', str_replace('"',"",$elements[0]));
        $placemark->appendChild($name);
        $point = $dom->createElement('Point');
        $placemark->appendChild($point);
        $coordinates = $dom->createElement('coordinates', DMStoDec($elements[1], $elements[2]));
        $point->appendChild($coordinates); 
    } else if ($state == "skip") {
        continue;
    } else if ($state == "artcc" || $state == "artcc high" || $state == "artcc low") {
        $placemark = $dom->createElement('Placemark');

        if ($state == "artcc") {
            $folderARTCC->appendChild($placemark);
        } else if ($state == "artcc low") {
            $folderARTCCLow->appendChild($placemark);
        } else if ($state == "artcc high") {
            $folderARTCCHigh->appendChild($placemark);
        }

        $name = $dom->createElement('name', $elements[0]);
        $placemark->appendChild($name);
        $lineString = $dom->createElement('LineString');
        $placemark->appendChild($lineString);
        
        $prevCoord = DMStoDec($elements[3], $elements[4]);
        $coordinateStr = DMStoDec($elements[1], $elements[2]) . " " . $prevCoord;

        $j = $i + 1;
        while ($j < sizeof($cleanLines)) {
            $nextLine = $cleanLines[$j];
            $validNextLine = count(preg_split('/\s+/', $nextLine));
            if ($validNextLine > 4) {
                $nextElements = preg_split('/\s+/', $nextLine);
                $nextCoordinate = DMStoDec($nextElements[1], $nextElements[2]);
                if ($nextCoordinate == $prevCoord) {
                    $prevCoord = DMStoDec($nextElements[3], $nextElements[4]);
                    $coordinateStr .= " " . $prevCoord;
                    $j++;
                } else {
                    break;
                }
            } else {
                break;
            }
       }
        $i = $j - 1;
        $coordinates = $dom->createElement('coordinates', $coordinateStr);
        $lineString->appendChild($coordinates);
    } else if ($state == "sid" || $state == "star") {
        $pattern = "~(N|S)[0-9]{1,3}\.[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}~";
        if (count($elements) > 4 && !preg_match($pattern, $elements[0])) { 
            $folderSIDName = $dom->createElement('Folder');
            $elemi = 1;
            $nameString = $elements[0];
            while (!preg_match($pattern, $elements[$elemi])) {
                $nameString = $nameString." ".$elements[$elemi];
                $elemi++;
            }

            $name = $dom->createElement('name', $nameString);
            $folderSIDName->appendChild($name);
            if ($state == "sid")
                $folderSID->appendChild($folderSIDName);
            else
                $folderSTAR->appendChild($folderSIDName);

            $j = $i + 1;

            while ($j < sizeof($cleanLines)) {
                $placemark = $dom->createElement('Placemark');
                $nextLine = preg_split('/\s+/', $cleanLines[$j]);
                if (preg_match($pattern, $nextLine[0])) { //adding sid points
                    $lineString = $dom->createElement('LineString');
                    $placemark->appendChild($lineString);
                    
                    $prevCoord = DMStoDec($nextLine[2], $nextLine[3]);
                    $coordinateStr = DMStoDec($nextLine[0], $nextLine[1]) . " " . $prevCoord;


                    $k = $j + 1;
                    while ($k < sizeof($cleanLines)) {
                        $nextLine = preg_split('/\s+/', $cleanLines[$k]);
                        if (preg_match($pattern, $nextLine[0])) {
                            $nextCoordinate = DMStoDec($nextLine[0], $nextLine[1]);
                            if ($nextCoordinate == $prevCoord) {
                                $prevCoord = DMStoDec($nextLine[2], $nextLine[3]);
                                $coordinateStr .= " " . $prevCoord;
                                $k++;
                            } else {
                                break;
                            }
                        } else {
                            break;
                        }
                   }
                    $j = $k - 1;

                      
                    $coordinates = $dom->createElement('coordinates', $coordinateStr);
                    $lineString->appendChild($coordinates);
                    $folderSIDName->appendChild($placemark);
                    $j++;
                } else {
                    break;
                }
            }
            $i = $j - 1;
        }
    } else if ($state == "geo") {
        $placemark = $dom->createElement('Placemark');

        $folderGEO->appendChild($placemark);

        $lineString = $dom->createElement('LineString');
        $placemark->appendChild($lineString);
        
        $prevCoord = DMStoDec($elements[2], $elements[3]);
        $coordinateStr = DMStoDec($elements[0], $elements[1]) . " " . $prevCoord;

        $j = $i + 1;
        while ($j < sizeof($cleanLines)) {
            $nextLine = $cleanLines[$j];
            $validNextLine = count(preg_split('/\s+/', $nextLine));
            if ($validNextLine > 4) {
                $nextElements = preg_split('/\s+/', $nextLine);
                $nextCoordinate = DMStoDec($nextElements[0], $nextElements[1]);
                if ($nextCoordinate == $prevCoord) {
                    $prevCoord = DMStoDec($nextElements[2], $nextElements[3]);
                    $coordinateStr .= " " . $prevCoord;
                    $j++;
                } else {
                    break;
                }
            } else {
                break;
            }
       }
        $i = $j - 1;
        $coordinates = $dom->createElement('coordinates', $coordinateStr);
        $lineString->appendChild($coordinates);
    }
}

$domFolders = $document->childNodes;
$foldersToRemove = array();

foreach ($domFolders as $folder) {
  if (count($folder->childNodes) == 1)
    $foldersToRemove[] = $folder;
}

foreach( $foldersToRemove as $folder ){
  $folder->parentNode->removeChild($folder);
}
    

$dom->save("sector.kml");