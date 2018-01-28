<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>{{ page_title }}</title>

        <script type="text/javascript" src="{{ node_url }}/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="{{ script_url }}/ajax.js"></script>
        <script type="text/javascript" src="{{ script_url }}/switch.js"></script>

        <link rel="stylesheet" type="text/css" href="{{ stylesheet_url }}/main.css" />

        <script type="text/javascript">
            // URLs and paths
            const script_url = '{{ script_url }}';
            const base_url = '{{ base_url }}';

            // Language strings should go below here
            const could_not_load_module = '{{ _translate('could_not_load_module') }}';
        </script>
    </head>

    <body>
      <header id="header">
          {% if is_logged_in %}
            <a class="button" href="{{ base_url }}/index.php/logout">{{ _translate('logout') }}</a>
            <a class="button" href="{{ base_url }}/index.php/download">{{ _translate('download') }}</a>
            <input type="button" class="switchBtn" id="tempBtn" value="Temperature"/>
            <input type="button" class="switchBtn" id="rainBtn" value="Rain"/>
          {% endif %}
          <h1 id=PageTitle align="center">Petrozgaz</h1>
      </header>
