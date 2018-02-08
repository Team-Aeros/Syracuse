/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 *
 * Switches the display to the Rain data tab or the temperature data tab
 */

$(document).ready(function () {
    $("#temp").hide();
    $("#button_temp").click(function () {
        $("#temp").show();
        $("#rain").hide();
        initialize();
    });
    $( "#button_rain").click(function () {
        $("#temp").hide();
        $("#rain").show();
    });
});
