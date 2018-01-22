<div id = "login_body"
     class="login_body"
     align="center">

    <!-- display error if there are errors in Auth->getErrors() -->

    <form class="form-horizontal" action="{{ base_url }}/index.php/login"  method="POST">
        <div class="form-group">
            <input class="input" type="text" placeholder="E-mail" name="email" size="20">
        </div>
        <div class="form-group">
            <input class="input" type="password" placeholder="Password" name="password" size="20">
        </div>
        <br>
        <input type="submit" value="Log in">
    </form>

</div>



