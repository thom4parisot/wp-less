# Advanced Usage

This part of the documentation will help you adapting `wp-less` plugin to your very own needs without breaking anything.

## Registering a LESS variable

You can inject [LESS variables](http://leafo.net/lessphp/docs/#variables) before the compilation process. You can then use **dynamic variables** defined upon WordPress settings or your own business logic.

This can be performed with 2 methods of the `WPLessPlugin` class:
* `addVariable($name string, $value string|number)`: sets 1 LESS variable value;
* `setVariables($variables array)`: sets several LESS variables value at one.

```php
// wp-content/themes/your-theme/functions.php

if (class_exists('WPLessPlugin')){
	$less = WPLessPlugin::getInstance();

	$less->addVariable('myColor', '#666');
	// you can now use @myColor in your *.less files

	$less->setVariables(array(
		'myColor' => '#777',
		'minSize' => '18px'
	));
	// you can now use @minSize in your *.less files
	// @myColor value has been updated to #777
}
```

## Registering a LESS function

You can inject [custom LESS functions](http://leafo.net/lessphp/docs/#custom_functions) before the compilation process. You can now package LESS helpers for your theme in a very useable way (even as WordPress plugins).

This can be performed with 1 method of the `WPLessPlugin` class:
* `registerFunction($name string, $callback string)`: binds a PHP callback function to a LESS function.

```php
// wp-content/themes/your-theme/functions.php

if (class_exists('WPLessPlugin')){
	$less = WPLessPlugin::getInstance();

	function less_generate_random($max = 1000){
		return rand(1, $max);
	}

	$less->registerFunction('random', 'less_generate_random');
	// you can now use random() in your *.less files, like
	// div.random-size{
	// 	width: less_generate_random(666);
	// }
}
```

**Notice**: don't forget the handy [native LESS functions](http://leafo.net/lessphp/docs/#built_in_functions).

## Changing compilation target directory

By default `wp-less` will outputs compiled CSS to your WordPress upload folder (by default: `wp-content/uploads/wp-less`).  
It’s done this way because this folder is usually available in *write mode*, even with tricky filesystem permissions.

You can alter the compile path both for filesystem and URIs. It is usefull if you have a CDN for theme assets or if your browser path is different than the filesystem one.

This can be performed with 2 methods of the `WPLessConfiguration` class:
* `setUploadDir($dir string)`: sets the new compile filesystem directory;
* `setUploadUrl($url string)`: sets several LESS variables value at one.

```php
// wp-content/themes/your-theme/functions.php

if (class_exists('WPLessPlugin')){
	$lessConfig = WPLessPlugin::getInstance()->getConfiguration();

	// compiles in the active theme, in a ‘compiled-css’ subfolder
	$lessConfig->setUploadDir(get_stylesheet_directory() . '/compiled-css');
	$lessConfig->setUploadUrl(get_stylesheet_directory_uri() . '/compiled-css');
}
```
