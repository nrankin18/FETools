var convertStatus;

window.onload = function () {
    convertStatus = document.getElementById('statusConvert');
    createStatus = document.getElementById('statusNew');
};

function showEditFile() {
    document.getElementById('newFile').style.display = "none";
    document.getElementById('convertFile').style.display = "block";
}

function showNewFile() {
    document.getElementById('newFile').style.display = "block";
    document.getElementById('convertFile').style.display = "none";
}

function disableInput() {
    document.getElementById('submitConvert').disabled = true;
    document.getElementById('convertFile').disabled = true;
    document.getElementById('convertText').disabled = true;
    document.getElementById('createText').disabled = true;
    document.getElementById('submitNew').disabled = true;
}

function showConvertLoading() {
    document.getElementById('statusConvert').style.visibility = "visible";
    document.getElementById('loaderConvert').style.visibility = "visible";
}

function showNewLoading() {
    document.getElementById('statusNew').style.visibility = "visible";
    document.getElementById('loaderNew').style.visibility = "visible";
}

function enableInput() {
    document.getElementById('submitConvert').disabled = false;
    document.getElementById('convertSctFile').disabled = false;
    document.getElementById('convertText').disabled = false;
    document.getElementById('statusConvert').style.visibility = "hidden";
    document.getElementById('loaderConvert').style.visibility = "hidden";
    document.getElementById('statusNew').style.visibility = "hidden";
    document.getElementById('loaderNew').style.visibility = "hidden";
    document.getElementById('createText').disabled = false;
    document.getElementById('submitNew').disabled = false;


}

function convertFile() {
    disableInput();
    convertStatus.innerHTML =  "Reading file...";
    console.log(convertStatus);
    showConvertLoading();

    let sector = document.getElementById('convertText').value;
    
    let file = document.getElementById("convertSctFile").files[0];
    if (file) {
        convertStatus = "Reading file...";
        const reader = new FileReader();
        reader.onload = function(e) {
            sector = reader.result;   
            convertStatus.innerHTML =  "Converting file...";
            sendConvert(sector);
        }
        reader.readAsText(file);
    } else {
        sendConvert(sector);
    }
}

function sendConvert(sector) {
    $.ajax({
        type:"post",
        url:"genKML.php",
        data: {
            sector: sector
        },
        cache:false,
        success: function () {
            convertStatus.innerHTML =  "Downloading file...";
            const link = document.createElement("a");
            link.download = "sector.kml";
            link.href = "sector.kml";
            link.click(); 
            enableInput();
        }
    });
}

function createFile() {
    showNewLoading();
    createStatus.innerHTML = "Loading data...";
    disableInput();
    const infoName = document.getElementById('info-name').value;
    const infoCallsign = document.getElementById('info-callsign').value;
    const infoAirport = document.getElementById('info-airport').value;
    const infoLat = document.getElementById('info-latitude').value;
    const infoLong = document.getElementById('info-longitude').value;
    const infoNMLat = document.getElementById('info-nmLat').value;
    const infoNMLong = document.getElementById('info-nmLong').value;
    const infoMV = document.getElementById('info-mv').value;
    const infoScale = document.getElementById('info-scale').value;
    const rootKML = document.getElementById('createText').value;
    createStatus.innerHTML = "Creating file...";
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
            createStatus.innerHTML = "Downloading file...";
            if (infoName)
                download((infoName + ".sct2"), html);
            else 
                download(("sector.sct2"), html);
            enableInput();
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