$(document).ready(function () {
    $("#temp").hide();
    $("#button_temp").click(function () {
        $("#temp").show();
        $("#rain").hide();
    });
    $( "#button_rain").click(function () {
        $("#temp").hide();
        $("#rain").show();
    });
});
