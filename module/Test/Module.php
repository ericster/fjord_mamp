<?php
// module/Test/Module.php
namespace Test;
// Add this import statement:
use Test\Model\DeviceTable;
use Test\Model\IssueTable;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Test\Model\DeviceTable' =>  function($sm) {
//     						$dbAdapter = $sm->get('adapter');
    						$dbAdapter = $sm->get('adapter_redmine');
    						$table     = new DeviceTable($dbAdapter);
    						return $table;
    					},
    					'Test\Model\IssueTable' =>  function($sm) {
//     						$dbAdapter = $sm->get('adapter');
    						$dbAdapter = $sm->get('adapter_redmine');
    						$table     = new IssueTable($dbAdapter);
    						return $table;
    					},
    			),
    	);
    }
}
