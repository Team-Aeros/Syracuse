/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

function perform_request(ajax_url, data_to_send, handler, get_or_post = 'GET', data_type = 'html') {
    $.ajax({
        data: data_to_send,
        dataType: data_type,
        url: base_url + ajax_url,
        method: get_or_post,
        success: handler,
        error: function() {
            alert(could_not_load_module);
        }
    });
}

function load_module(element, url) {
    request = perform_request(url, {}, function(message, data, response) {
        $(element).html(response.responseText);
    });
}

function load_rain_data(url) {
    var tds = ['td1','td2','td3','td4','td5','td6','td7','td8','td9','td10'];
    perform_request(url, {}, function(message, data, response) {
        let i = 0;
        console.log(response.responseJSON);
        $.each(response.responseJSON, function() {
            document.getElementById(tds[i]).innerHTML = this.station + ': ' + this.precipitation + " mm";
            i++;
        });
    }, 'GET', 'json');
    for (i=0; i < tds.length; i++) {
        var td = document.getElementById(tds[i]);
        td.innerHTML = "No more data";
    }
}

