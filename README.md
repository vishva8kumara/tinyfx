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
 This "works" (with a small warning on httpd (re)start - just ignore that), and saves at least half a second on every HTTP request.

index.php loads config file and router script.
Router breaks down the HTTP request, load the module, call the function (with parameters), and render the view.

Configuration
------
If you are using .htaccess file, then you must change it to direct all HTTP requests to yhr index.php file.
The default .heaccess file is written for a project residing on /tinyfx/ folder in www-root. Change '/tinyfx/' to the working folder.
There is an exception to the above redirect; we are allowing the /public/ folder where we would store static files such as css, js and images etc..
If you wish to use a cookieless subdomain (maybe on production server), just remove this line.

config.php file is in tinyfx framework folder. Any variable or constant you would like to use throughout the application can be declared here.

The $base_url and $base_url_public from config.php are made available on views to use in urls and embedding images, js and css etc..
$base_url should be set to the URL where the project is available through HTTP. $base_url will be re-written with the sub-domain (if a sub-domain name is found on Request URI).
$base_url_public is where you would store static content (css, js, images etc..). If you plan to use a cookieless static subdomain (we highly encourage), set that URL here.
$public_files_root is the relative (file/directory) path to the contents of this static sub-domain contents.
This is useful when uploading files through the web site which should be available through the static sub-domain.
We highly recommend declairing that path here if you would plan to implement a file upload.

$domain should be the project folder name in develpment environment, and full domain name +.com in production environment. Do not use www. (subdomain here - unless you wish to use sub-sub-domains).
A global variable $subdomain will be available to the module if there were any subdomain name on the HTTP Request URI.

$minify_html
Setting this variable to true will remove all unnecessory whitespaces from the HTTP response. This can save up to 30% of server outgoing traffic.

$compress_html
Setting this variable to true will compress the HTTP response with x-gzip or gzip, if the web browser accept data compression. This setting also saves server outgoing traffic.

$disallow_hotlinking, $minify_icluded_js, $minify_icluded_css are diclared in the sample code, but used in the CDN framework, which is yet not released to the public.

Getting Started
------
 http://website.com/module/method/param0/param1/param2
In this example, the first literal after the site domain is the module. There must be a file in this name +.php in the modules folder.

In the module you may specify a template. By default we have home.php and error_404.php templates. error_404 template reuses the home template, only rewriting the page body.
Template are in templates folder. They should start with <html> and end with </html> and must have <?= $yield; ?> somewhere in the middle. This is where the rendered view is put.

The second literal is the method/function. There must be a function in this name inside that module which accept one parameter.
The rest of the literals are considered as parameters to this function, and passed in as an array.
POST parameters are also passed in to this first parameter of the function, and can be accessed by array keys (associative array).

The function should return an associative array. This array is converted to php variables for the view.

In the sample code, you can find the module index.php and view index/index.php
This is the home page of your web site. Since the router does not find a module name, it loads the index module.
Since the framework does not find a method name it calls the index function, and renders the index/index.php view.

Global functions
------
Using a database is made easy by a Database connection. Set the database connection credentials on the config.php, and call connect_database() function.
connect_database() returns an object [this is singleton - consecutive calls would return the same object; doesn't take extra memmory].
$db = connect_database();

There are 4 basic functions in this object that would be useful to you.

	$db->query('SELECT * FROM table1');
This simply runs the query and returns the data set. You may use mysql_fetch_array to go though the result rows.

	$db->insert('table1', array('name' => 'Vasana Pathirana', 'address' => '234, Near the Mountain, Summer Land', 'no-such-column' => 'It\'s ok :-)'));
To insert a record to the table1 we can pass an associative array. If you have elements in the array that are not column of the table, just don't worry, those are just ignored.
But you have to give an array element for all not-null fields. We recommend making an auto increment primary key 'id' for every table. This is going to be useful in update/delete.
Let's say your use submits a form with values you want to insert to a table. You may just write as:
	$db->insert('table1', $params);

	$db->update('table1', array('id' => 333, 'name' => 'Vishva Kumara', 'telephone' => '94774580316', 'not_a_column' => 'this will be ignored'));
	$db->update('table2', array('name' => 'Tea Pot', 'owner' => 'Russel', 'location' => 'unknown'), 'some_id = 666');
Just like an insert we can update a database table from this function. If the array contain an element 'id', it is used to find which record is to update.
If you are not updating row by 'id', then the third parameter should specify the condition to select the records to update.
Let's say your user submits a form with values you want to update on a table.
If you keep a hidden input field with the record ID, you may just write as:
	$db->update('table1', $params);
And if you are concerned about the security and access-control of records on that table, we recomment using the third parameter in this way.
	$db->update('table1', $params, 'id = '.$params['id'].' AND owner_id = '.$user_id);

$user_id is a global variable set by interfaces/user_tracking.php

redirect($module, $method, $params, $redirect_after)
This is a simple function to send an HTTP redirect. Only the first parameter is required.
Teh fourth parameter $redirect_after is when you want to sign in the user and redirect after sign-in.
This parameter is set to $_SESSION['REDIRECT_AFTER_SIGNIN']

User identification
------
Create a user sign-in experience similar to what Facebook and Google uses, up-to their standards. This is a simple interface you may use to acieve that.
Just include 'interfaces/user_tracking.php'; on the top of your module, in all modules where you want to track users.
It sets a cookie with some random hash with maximum life across all your sub-domains.
The value of this cookie is available as $user_id global variable. This $user_id is not dependent on any database record. It is actually a Browser ID.
If you would like to maintain a database table of users who would sign-in, we recommend two database tables:
	user (id, auth_level, approved, full_name, username, password, email)
	login (user_id, browser_id, require_password, active, last_login)
The codes to auto-log-in a user are commented in the user_tracking.php file. You may figure that out and modify according to your requirement.

When these two tables are set up properly (and the codes un-commented), you can show the user where he has signed-in from, and end sessions.
Let's say one of your users have logged in from a public browser, and he forgot to sign out.
You can show him all his records on login table, and allow him to set 'login' to 0.
Even though someone is using his user login on that browser at the moment, that session will be terminated immidiately.

If you have uncommented and modified the above mentioned code block, you will have another public variable $user available for users who has signed-in.
If this is !isset($user), that means the user is not signed-in. You may redirect them to login page.
When you redirect a user to login, don't forget to set the $_SESSION['REDIRECT_AFTER_SIGNIN'], or pass that as the fouth parameter to the redirect function.

$user is an array which contains the values of user record in database table.

