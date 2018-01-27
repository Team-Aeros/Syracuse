<div id = "login_body"
     class="login_body"
     align="center">

    <form class="form-horizontal" action="{{ base_url }}/index.php/login"  method="POST">
        <div class="form-group">
            <input class="input" type="text" placeholder="E-mail" name="email" size="25">
        </div>
        <div class="form-group">
            <input class="input" type="password" placeholder="Password" name="password" size="25">
        </div>
        <br>
        <input type="submit" value="Log in">
    </form>
    {% if errors is not empty %}
        <p>{{ errors.cred }}</p>
    {% endif %}
</div>