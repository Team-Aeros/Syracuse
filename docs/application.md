# Syracuse
Syracuse is the name of this web application. It was developed by Aeros Development for PGH in Haïti.<br><br>
On this website the client can view the data that they requested. Contents of the data are explained in data_handling.

## Structure
This web application uses a module based structure. This means that each page that is displayed is its own module that can be loaded.
This gives the entired application a solid structure and means it can be easily expanded to display more data if needed.

## Limitations
In the first stages of the development Aeros Development intended to do this using twig and FastRoute, but because of limitations given by HanzeHogeschool this was not possible.
Because of this Aeros Development decided to write its own barebone code to replace twig, and FastRoute.

## Template compilation
In order to keep the templates clean, we wrote our own template compiler, which, as the name suggests, compiles templates and caches them. The syntax is similar to Twig's, but there are some differences (see ``templates.md`` for more information).

## Routing
We wrote a custom routing class that parses the URL and returns the parameters. This allows us to use URLs like /index.php/help without relying on ``.htaccess`` files.