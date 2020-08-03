var convertStatus;
var createStatus;
var requireNavInfo = false;

window.onload = function() {
    createStatus = document.getElementById('createStatus');
    convertStatus = document.getElementById('convertStatus');
};

function showConvertWindow() {
    hideWindows();
    document.getElementById('convertWindow').style.display = "block";
}

function showHelpWindow() {
    hideWindows();
    document.getElementById('helpWindow').style.display = "block";
}

function showCreateWindow() {
    hideWindows();
    document.getElementById('createWindow').style.display = "block";
}

function hideWindows() {
    document.getElementById('convertWindow').style.display = "none";
    document.getElementById('helpWindow').style.display = "none";
    document.getElementById('createWindow').style.display = "none";

}

function disableSubmit() {
    document.getElementById('createSubmit').disabled = true;
    document.getElementById('convertSubmit').disabled = true;
}

function showCreateLoading() {
    document.getElementById('createStatus').style.visibility = "visible";
    document.getElementById('createLoader').style.visibility = "visible";
}

function showConvertLoading() {
    document.getElementById('convertStatus').style.visibility = "visible";
    document.getElementById('convertLoader').style.visibility = "visible";
}

function enableSubmit() {
    document.getElementById('createLoader').style.visibility = "hidden";
    document.getElementById('createStatus').style.visibility = "hidden";

    document.getElementById('convertLoader').style.visibility = "hidden";
    document.getElementById('convertStatus').style.visibility = "hidden";

    document.getElementById('createSubmit').disabled = false;
    document.getElementById('convertSubmit').disabled = false;
}

function testRequireNavInfo() {
    if ($('#navAirports').get(0).files.length === 0 &&
        $('#navNavaids').get(0).files.length === 0 &&
        $('#navWaypoints').get(0).files.length === 0 &&
        $('#navATS').get(0).files.length === 0) {
        requireNavInfo = false;
    } else {
        requireNavInfo = true;
    }

}

$(document).ready(function() {
    $('#navAirports').on("change", function() {
        testRequireNavInfo();
    });
    $('#navNavaids').on("change", function() {
        testRequireNavInfo();
    });
    $('#navWaypoints').on("change", function() {
        testRequireNavInfo();
    });
    $('#navATS').on("change", function() {
        testRequireNavInfo();
    });
});


async function createFile() {
    const navLatCenter = document.getElementById('navLatCenter').value;
    const navLongCenter = document.getElementById('navLongCenter').value;
    const navRadius = document.getElementById('navRadius').value;

    if (requireNavInfo && (navLongCenter.length < 1 || navLongCenter.length < 1 || navRadius.length < 1)) {
        alert("Please enter location information to filter Navigraph data");
        return false;
    }

    disableSubmit();
    createStatus.innerHTML = "Loading data...";
    showCreateLoading();

    const infoName = document.getElementById('infoName').value;
    const infoCallsign = document.getElementById('infoCallsign').value;
    const infoAirport = document.getElementById('infoAirport').value;
    const infoLat = document.getElementById('infoLat').value;
    const infoLong = document.getElementById('infoLong').value;
    const infoNMLat = document.getElementById('infoNMLat').value;
    const infoNMLong = document.getElementById('infoNMLong').value;
    const infoMV = document.getElementById('infoMV').value;
    const infoScale = document.getElementById('infoScale').value;
    const kmlText = document.getElementById('kmlText').value;



    createStatus.innerHTML = "Reading airports.txt...";
    const navAirports = await readFile(document.getElementById("navAirports").files[0]);
    createStatus.innerHTML = "Reading navaids.txt...";
    const navNavaids = await readFile(document.getElementById("navNavaids").files[0]);
    createStatus.innerHTML = "Reading waypoints.txt...";
    const navWaypoints = await readFile(document.getElementById("navWaypoints").files[0]);
    createStatus.innerHTML = "Reading ats.txt...";
    const navATS = await readFile(document.getElementById("navATS").files[0]);

    createStatus.innerHTML = "Creating file...";
    $.ajax({
        type: "post",
        url: "create.php",
        data: {
            infoName: infoName,
            infoCallsign: infoCallsign,
            infoAirport: infoAirport,
            infoLat: infoLat,
            infoLong: infoLong,
            infoNMLat: infoNMLat,
            infoNMLong: infoNMLong,
            infoMV: infoMV,
            infoScale: infoScale,
            kmlText: kmlText,

            navLatCenter: navLatCenter,
            navLongCenter: navLongCenter,
            navRadius: navRadius,

            navAirports: navAirports,
            navNavaids: navNavaids,
            navWaypoints: navWaypoints,
            navATS: navATS
        },
        cache: false,
        success: function(html) {
            createStatus.innerHTML = "Downloading file...";
            if (infoName)
                download((infoName + ".sct2"), html);
            else
                download(("sector.sct2"), html);
            enableSubmit();
        }
    });
}

async function convertFile() {
    disableSubmit();
    convertStatus.innerHTML = "Loading data...";
    showConvertLoading();

    let sctText = document.getElementById('sctText').value;

    let sctFile = document.getElementById("sctFile").files[0];
    if (sctFile) {
        convertStatus.innerHTML = "Reading file...";
        sctText = await readFile(sctFile);
    }

    $.ajax({
        type: "post",
        url: "convert.php",
        data: {
            sct: sctText
        },
        cache: false,
        success: function() {
            convertStatus.innerHTML = "Downloading file...";
            const link = document.createElement("a");
            link.download = "sector.kml";
            link.href = "sector.kml";
            link.click();
            enableSubmit();
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

function readFile(file) {
    if (file) {
        return new Promise((resolve, reject) => {
            let reader = new FileReader();
            reader.onload = () => {
                resolve(reader.result);
            };
            reader.onerror = reject;
            reader.readAsText(file);
        })
    }
    return "";
}