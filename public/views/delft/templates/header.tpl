<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>{{ page_title }}</title>

        <script type="text/javascript" src="{{ node_url }}/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="{{ script_url }}/ajax.js"></script>

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
          <button
              type="button"
                class="float_right">Log out</button>
          <h1 align="center">Just leaving this here for testing purposes</h1>
      </header>
