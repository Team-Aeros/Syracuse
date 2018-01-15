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

            // Language strings should go below here
            const could_not_receive_json_data = '{{ _translate('could_not_receive_json_data') }}';
        </script>
    </head>

    <body>
      <header id="header">
          <button
              type="button"
                class="float_right">Log out</button>
          <h1 align="center">Just leaving this here for testing purposes</h1>
      </header>

      <div id="container">
          <div id="list_1" class="top10List widget">
              <h2>Top 10 rain list</h2>
              <p>A fuck ton.</p>
          </div>

          <div id="map" class="widget">
              <h2>Map of Caribbean</h2>
              <p> no map :(</p>
          </div>

          <div id="list_2" class="widget top10List">
              <h2>Graph</h2>
              <p>Temperature</p>
          </div>

          <br class="clear" />
      </div>

      <footer id="footer">
          <p>Aeros Development</p>

    </body>
</html>
