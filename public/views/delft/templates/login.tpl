<!DOCTYPE html>
<html>
<head>
    <title>Syracuse</title>
    <link rel="stylesheet" type="text/css" href="http://localhost/syracuse/public/views/delft/css/styling.css">
</head>
<body class="body">
<div id = container
     class="container"
     align="center">
    <div id = header
         class="header">
        <h1>PGH</h1>
    </div>
    <form class="form-horizontal" action="login.php" method="POST">
        <div class="form-group">
            <input class="input" type="text" placeholder="E-mail" value="" name="email" size="20">
        </div>
        <div class="form-group">
            <input class="input" type="password" placeholder="Password" name="password" size="20">
        </div>
        <br>
        <input class="btn btn-success" type="submit" value="Log in">
    </form>
</div>
</body>
</html>


