# Differences between Twig and our custom engine
* Filters are called like this: variable|filterfunc. There must not be a space before/after the pipe symbol
* The show() method of template (and thus displayTemplate() in the GUI class) prints the result directly to the screen rather than returning the result