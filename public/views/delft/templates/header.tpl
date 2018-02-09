<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>{{ page_title }}</title>
        <p id = "ghostDataTitle"></p>

        <script type="text/javascript" src="{{ node_url }}/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="{{ node_url }}/chart.js/dist/Chart.min.js"></script>
        <script type="text/javascript" src="{{ script_url }}/ajax.js"></script>
        <script type="text/javascript" src="{{ script_url }}/switch.js"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>-->

        <link href="{{ node_url }}/notosans-fontface/css/notosans-fontface.css" rel="stylesheet" /> 
        <link rel="stylesheet" type="text/css" href="{{ stylesheet_url }}/main.css" />
        {% if on_login_page %}<link rel="stylesheet" type="text/css" href="{{ stylesheet_url }}/login.css" />{% endif %}

        <script type="text/javascript">
            // URLs and paths
            const script_url = '{{ script_url }}';
            const base_url = '{{ base_url }}';

            // Language strings should go below here
            const could_not_load_module = '{{ _translate('could_not_load_module') }}';

            $("#ghostDataTitle").hide();
        </script>

        <link rel="icon" href="{{ base_url }}/favicon.png" sizes="16x16" type="image/png" />
    </head>

    <body>
        {% if is_logged_in %}
        <div id="menu" class="float_right col33">
            <a id="button_rain" class="button switchBtn">{{ echo _translate('rain') }}</a>
            <a id="button_temp" class="button switchBtn">{{ echo _translate('temperature') }}</a>
            <a id="button_download" class="button" href="{{ base_url }}/index.php/download">{{ echo _translate('download') }}</a>
            <a id="button_logout" class="button" href="{{ base_url }}/index.php/logout">{{ echo _translate('logout') }}</a>
        </div>
        {% endif %}
        <header id="header">
            <h1 id="page_title" class="float_left col66"><a href="{{ base_url }}">Pétrogaz</a></h1>
            <br class="clear" />
        </header>
