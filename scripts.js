function showEditFile() {
    document.getElementById('newFile').style.display = "none";
    document.getElementById('editFile').style.display = "block";
}

function showNewFile() {
    document.getElementById('newFile').style.display = "block";
    document.getElementById('editFile').style.display = "none";
}

function genKMLFile() {
    const sector = document.getElementById('reverseTxt').value;
    $.ajax({
        type:"post",
        url:"genKML.php",
        data: {
            sector: sector
        },
        cache:false,
        success: function () {
            const link = document.createElement("a");
            link.download = "sector.kml";
            link.href = "sector.kml";
            link.click();
        }
    });
}

function genNewFile() {
    const infoName = document.getElementById('info-name').value;
    const infoCallsign = document.getElementById('info-callsign').value;
    const infoAirport = document.getElementById('info-airport').value;
    const infoLat = document.getElementById('info-latitude').value;
    const infoLong = document.getElementById('info-longitude').value;
    const infoNMLat = document.getElementById('info-nmLat').value;
    const infoNMLong = document.getElementById('info-nmLong').value;
    const infoMV = document.getElementById('info-mv').value;
    const infoScale = document.getElementById('info-scale').value;
    const rootKML = document.getElementById('root-KML').value;
    $.ajax({
        type:"post",
        url:"genNew.php",
        data: {
            name: infoName,
            callsign: infoCallsign,
            airport: infoAirport,
            lat: infoLat,
            long: infoLong,
            nmLat: infoNMLat,
            nmLong: infoNMLong,
            mv: infoMV,
            scale: infoScale,
            rootKML: rootKML
        },
        cache:false,
        success: function (html) {
            if (infoName)
                download((infoName + ".sct2"), html);
            else 
                download(("sector.sct2"), html);
        }
    });
}

function download(filename, text) {
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}