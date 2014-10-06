<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overridding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// config/autoload/global.php:
return array(
    'db' => array(
    		'adapters' => array (
    				'adapter' => array(
				        'driver'         => 'Pdo',
				        'dsn'            => 'mysql:dbname=zf2tutorial;host=localhost',
						'username'       => 'root',
				        'password'       => 'root',
				        //'dsn'            => 'mysql:dbname=test;host=localhost',
				        'driver_options' => array(
				            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				        ),
    				),
    				'adapter_redmine' => array(
// 				        'driver'         => 'Mysqli',
				        'driver'         => 'Pdo',
				        'dsn'            => 'mysql:dbname=redmine_bak;host=localhost',
						'username'       => 'root',
				        'password'       => 'root',
				        'driver_options' => array(
				            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				        ),
    				),
    		)
    ),
    'service_manager' => array(
//         'factories' => array(
//             'Zend\Db\Adapter\Adapter'
//                     => 'Zend\Db\Adapter\AdapterServiceFactory',
//         ),
        'abstract_factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ),
    ),
);
