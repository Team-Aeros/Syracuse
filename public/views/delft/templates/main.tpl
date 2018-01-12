<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>{{ page_title }}</title>

        <script type="text/javascript" src="{{ node_url }}/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="{{ script_url }}/ajax.js"></script>

        <script type="text/javascript">
            // URLs and paths
            const script_url = '{{ script_url }}';

            // Language strings should go below here
            const could_not_receive_json_data = '{{ _translate('could_not_receive_json_data') }}';
        </script>
    </head>

    <body>
      <header id="header"
              style="alignment: top|center;
                             background-color: orangered;
                             padding: 25px 50px 25px 50px;">
          <button
              type="button"
              style="float: right;">Log out</button>
          <h1 align="center">PGH</h1>
      </header>
      <div id = "container">
          <div id = "top10List"
               style="float: left;
                          background-color: deepskyblue;
                          padding: 25px 50px 25px 50px;
                          height: 100px;
                          width: 100px;
                          margin-right: 33%;">
              <h2>Top 10 rain list</h2>
              <p>A fuck ton.</p>
          </div>

          <div id = "map"
               style="float: left;
                          background-color: green;
                          padding: 25px 50px 25px 50px;
                          height: 200px;
                          width: 200px;">
              <h2>Map of Caribean</h2>
              <p> no map :(</p>
          </div>

          <div id = "top10List"
               style="float: right;
                          background-color: brown;
                          padding: 25px 50px 25px 50px;
                          height: 100px;
                          width: 100px;
                          margin-left: 33%;">
              <h2>Graph</h2>
              <p>Temperature</p>
          </div>
      </div>
      <footer id = "footer"
              style="position: absolute; bottom: 0; width: 100%; height: 60px;
                  background-color: rebeccapurple;
                  padding: 5px 5px 5px 5px;">
          <p>Aeros Development</p>
      </footer>
    </body>
</html>
