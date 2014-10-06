<?php
// module/Album/Module.php
namespace Album;
// Add this import statement:
use Album\Model\AlbumTable;
class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	    // getAutoloaderConfig() and getConfig() methods here

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Album\Model\AlbumTable' =>  function($sm) {
//                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbAdapter = $sm->get('adapter');
                    $table     = new AlbumTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}