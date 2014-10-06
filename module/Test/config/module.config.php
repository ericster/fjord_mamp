<?php 
// module/Test/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Test\Controller\Device' => 'Test\Controller\DeviceController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'device' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/device[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Test\Controller\Device',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'device' => __DIR__ . '/../view',
        ),
        'strategies' => array(
        		'ViewJsonStrategy',
        ),
    ),
);