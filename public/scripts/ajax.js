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

function perform_request(ajax_url, data_to_send, handler) {
    $.ajax({
        data: data_to_send,
        dataType: 'json',
        url: script_url + '/index.php' + ajax_url,
        method: 'GET',
        success: handler,
        error: function() {
            alert(could_not_receive_json_data);
        }
    });
}