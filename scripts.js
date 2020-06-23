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
    const rootKML = document.getElementById('root-KML').value;
    $.ajax({
        type:"post",
        url:"genNew.php",
        data: {
            rootKML: rootKML
        },
        cache:false,
        success: function (html) {
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