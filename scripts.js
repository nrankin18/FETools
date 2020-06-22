function showEditFile() {
    // document.getElementById('newFile').style.display = "none";
    document.getElementById('editFile').style.display = "block";
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