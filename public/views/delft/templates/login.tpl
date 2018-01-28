<div id = "login_body"
     class="login_body">

    <h1>Login</h1>

    <div class="col-50 float_left">
        <div id="login_description">
            {{ _translate('this_is_the_login_page') }}
        </div>
        {% if errors is not empty %}
            <p>{{ errors.cred }}</p>
        {% endif %}
    </div>

    <div class="col-50 float_right">
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
    </div>

    <br class="clear" />
</div>