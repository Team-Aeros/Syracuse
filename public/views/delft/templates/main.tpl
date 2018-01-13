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
    <header id = header
            class = "header">
        <button
                type="button"
                class = "logout">
            Log out</button>
        <h1 align="center">PGH</h1>
    </header>
    <div id = container>
        <div id = top10List
             class = "topList">
            <h2>Top 10 rain list</h2>
            <p>A fuck ton.</p>
        </div>

        <div id = map
             class = "map">
            <h2>Map of Caribean</h2>
            <p> no map :(</p>
        </div>

        <div id = graph
             class = "graph">
            <h2>Graph</h2>
            <p>Temperature</p>
        </div>
    </div>
    <footer id = footer
            class = "footer">
        <p>Aeros Development</p>
    </footer>
    </body>
</html>
