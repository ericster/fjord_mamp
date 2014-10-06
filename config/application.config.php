<?php
return array(
    'modules' => array(
        'Application',
		'Album',                  // <-- Add this line
		'SanAuth',                  // <-- Add this line
		'ZF2FileUploadExamples',                  // <-- Add this line
		'TestAjax',                  // <-- Add this line
		'Test',                  // <-- Add this line
// 		'Formgen',                  // <-- Add this line
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
// 	    '/Applications/MAMP/htdocs/myapp/module/',
            './vendor',
        ),
    ),
);
