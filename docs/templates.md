# Templates

## How to create a new template
To create a new template, add a new file to ``/public/views/{theme}/templates``. It should have the ``.tpl`` extension.

## How to call a template
Templates can be called by invoking the ``getTemplate()`` method of the GUI class. For example:

```
    public function display() : void {
        self::$gui->displayTemplate('help', $this->_model->getData());
    }
```

The template loader will attempt to load ``help.tpl`` and sets the variables returned by the ``getData()`` method in the model. This method returns an associative array. Apart from these variables, a set of standard variables are also added, such as the stylesheet URL, image URL and more.

## Differences between Twig and our custom engine
* Filters are called like this: variable|filterfunc. There must not be a space before/after the pipe symbol
* The show() method of template (and thus displayTemplate() in the GUI class) prints the result directly to the screen rather than returning the result
* User and PHP's standard functions can be called directly and do not have to be added manually
* The custom template engine throws regular Exceptions, but these are caught by the GUI class (meaning developers do not have to worry about these things)
* Functions that start with an underscore will have the return value echo'ed by default (for convenience's sake)

## Differences between filters and functions
Filters are expected to return something and have one parameter, as opposed to functions, which can have an unlimited number of parameters and aren't required to return something.