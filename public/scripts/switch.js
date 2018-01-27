$(document).ready(function () {
    $("#temp").hide();
    $("#tempBtn").click(function () {
        $("#temp").show()
        $("#rain").hide();
    });
    $( "#rainBtn").click(function () {
        $("#temp").hide()
        $("#rain").show();
    });
});
