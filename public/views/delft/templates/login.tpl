<div id = container
     class="container"
     align="center">
    <div id = header
         class="header">
        <h1>PGH</h1>
    </div>
    <form class="form-horizontal" action="{{ base_url }}/index.php/login"  method="POST">
        <div class="form-group">
            <input class="input" type="text" placeholder="Username" value="" name="username" size="20">
        </div>
        <div class="form-group">
            <input class="input" type="password" placeholder="Password" name="password" size="20">
        </div>
        <br>
        <input class="btn btn-success" type="submit" value="Log in">
    </form>
</div>



