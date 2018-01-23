# AJAX-based module loading
Widgets (small modules) are loaded through AJAX. This means that in order to load a widget, you need to call a Javascript function. This document describes how to do just that.

## What do I need to do to load an existing module as a widget?
First of all, keep in mind that when modules are loaded through AJAX, the template header and footer are removed automatically. In order words, you do not have to take care of the AJAX detection process. However, you do need to add a new route to the Route class (``/src/core/controllers/Route.class.php``). It should look something like this:

``$routeCollector->addRoute(['POST', 'GET'], '/help/ajax/{ajax_request}', 'help');``

You should replace both occurences of ``help`` with the desired module name. Then, in the template, make sure you have an (empty) element and call the following Javascript function:

``load_module('#list_1', '/index.php/help/ajax/getlist');``

Here, replace ``#list_1`` with the element's name, ``help`` with the desired module name and ``getlist`` with a name for the AJAX request.