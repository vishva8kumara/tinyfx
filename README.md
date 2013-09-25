tiny(fx)
======

Tiny php Framework - [Module - View - Template]

tiny(fx) is made by Vishva Kumara (me) after doing a project with CodeIgniter.
I wanted to make a much simpler and light weight php framework with all and only what I needed most.
tiny(fx) is made non-object-oriented, and will remain this way for performance reasons, and to be small and light weight.

Overview
------
All HTTP requests are received by the index.php on the root. This is usually done with an .htaccess file.
 Whenever possible (in a VPS or dedi), I would recommend setting DocumentRoot to index.php instead the folder, and not use any htaccess.
 This "works" (with a small warning), and saves at least half a second on every HTTP request.

index.php loads config file and router script. Router breaks down the HTTP request, load the module, call the function (with parameters), and render the view.

 http://website.com/module/function/param0/param1/param2
In this example, the first literal after the site domain is the module. There must be a file in this name +.php in the modules folder.

In the module you may specify a template. By default we have home.php and error_404.php templates. error_404 template reuses the home template, only rewriting the page body.
Template are in templates folder. They should start with <html> and end with </html> and must have <?= $yield; ?> somewhere in the middle. This is where the rendered view is put.

The second literal is the function. There must be a function in this name inside that module which accept one parameter.
The rest of the literals are considered as parameters to this function, and passed in as an array.
POST parameters are also passed in to this first parameter of the function, and can be accessed by array keys (associative array).

The function should return an associative array. This array is converted to php variables in the view.