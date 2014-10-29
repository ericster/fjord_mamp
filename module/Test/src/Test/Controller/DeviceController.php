<?php
namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Test\Form\DeviceMapValidator;
use Test\Form\DeviceMapForm;
use Zend\View\Model\JsonModel;
use Zend\Debug\Debug;
use Test\Model\Device;

class DeviceController extends AbstractActionController
{
	protected $deviceTable;
	protected $issueTable;

	public function getDeviceTable()
	{
		if (!$this->deviceTable) {
			$sm = $this->getServiceLocator();
			$this->deviceTable = $sm->get('Test\Model\DeviceTable');
		}
		return $this->deviceTable;
	}

	public function getIssueTable()
	{
		if (!$this->issueTable) {
			$sm = $this->getServiceLocator();
			$this->issueTable = $sm->get('Test\Model\IssueTable');
		}
		return $this->issueTable;
	}

	public function redexcelTaedAction()
	{
		$form = new DeviceMapForm('devicemap');
	
		$request = $this->getRequest();
		if ($request->isPost()) {
			$postData = array_merge_recursive(
					$request->getPost()->toArray()
			);
			print_r($postData);
			print_r("isPost in deviceMap\n");
	
			/*
			 * validation for taskName, regexPattern.
			*/
			$formValidator = new DeviceMapValidator();
			$inputfilter = $formValidator->getInputFilter();
			$form->setInputFilter($formValidator->getInputFilter());
			$form->setData($postData);
			if ($form->isValid()) {
				$data = $form->getData();
				print_r("\nform validated \n");
				print_r($data);
				$colValidator = new DeviceSetValidator();
				$colfilter = $colValidator->getInputFilter();
				/*
				 * validation for colletion fieldset: appName, regexPattern.
				*/
				if(isset($postData['deviceVal'])){
					$formvalid = true;
					$deviceVal = $postData['deviceVal'];
					foreach( $deviceVal as $device ) {
						$colfilter->setData($device);
						if ($colfilter->isValid()) {
							print_r("collection fieldset validated\n");
						}
						else{
							$formvalid = false;
							print_r("collection fieldset not validated\n");
						}
					}
					 
					/*
					 * Finally all fields are validated
					*/
					if($formvalid) {
						print_r("All FIELDS VALIDATED\n");
						// TODO: fetch data from query and display
					}
					else{
						print_r("Search Table input error\n");
					}
	
				}
			}
			else{
					$inputfilter->setData($postData);
                    $err_data = $inputfilter->getValidInput();
					print_r($err_data);
			}
		}

		return array(
			'form' => $form,
		);
	}

	public function indexAction()
	{
		return new ViewModel(array(
				'devices' => $this->getDeviceTable()->fetchAll(),
		));
	}

	public function devicesAction()
	{
		return new ViewModel(array(
				'devices' => $this->getDeviceTable()->fetchAll(),
		));
	}

	public function deviceListAction()
	{
		return new JsonModel(
				 $this->getDeviceTable()->fetchAll()
		);
	}

    public function deviceautocompleteAction()
    {
        return new ViewModel();
    }

    public function downloadAction() {
    	$response = $this->getResponse();
    	// 	    $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
    	// 	    $response->setContent('blablabla');
    
    	// 	    /Applications/MAMP/htdocs/myapp/public/python/
    
    	$xlsx_file_name = "./public/python/appBreakdown.xls";
    	if(file_exists($filename)) {
    		print_r("appBreakdown.xls exists");
    	}
    
    	$response->getHeaders()->addHeaders(array(
    	// 	    		'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    			'Content-Type' => 'application/vnd.ms-excel',
    			'Content-Disposition' => 'attachment;filename="appBreak.xls"',
    			// 	    		'Cache-Control' => 'max-age=0',
    	));
    	$response->setContent(file_get_contents($xlsx_file_name));
    
    	return $response;
    }
    
    public function download_sampleAction()
    {
    	/* get here all the data you need from the database
    	 * $size = size of the file you can get by readfile()
    	* $filename = 12f3f1aa1b11ec11dd1dd1.zip (with the path)
    	* $filename1 = example.zip
    	*/
    
    	if(file_exists($filename)) {
    		ob_start();
    		$size = readfile($filename);
    		$this->view->data = ob_get_clean();
    	}
    
    	header('Expires: Mon, 20 May 1974 23:58:00 GMT');
    	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    	header('Cache-Control: no-store, no-cache, must-revalidate');
    	header('Cache-Control: post-check=0, pre-check=0', false);
    	header('Cache-Control: private');
    	header('Pragma: no-cache');
    	header("Content-Transfer-Encoding: binary");
    	header("Content-type: binary/octet-stream");
    	header("Content-length: {$size}");
    	header("Content-disposition: attachment; filename=\"{$filename1}\"");
    
    	$this->_helper->layout()->disableLayout();
    	$this->render('download');
    }
    
	public function issueAction()
	{
		return new ViewModel(array(
				'issues' => $this->getIssueTable()->fetchAll(),
		));
	}
    public function getissuesAction()
    {
        return new ViewModel();
    }
    
    public function processAjaxRequestAction(){
    
    	$result = array('status' => 'error', 'message' => 'There was some error. Try again.');
    
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    
    	if($request->isXmlHttpRequest()){
    
    		$data = $request->getPost();
    		Debug::dump($data);
    
    		if(isset($data['deviceVal']) && !empty($data['deviceVal'])){
    			$result['status'] = 'success';
    			$result['message'] = 'We got the posted data successfully.';
    			$devices = $data['deviceVal'];
    			$deviceString = '';
    			foreach ($devices as $device){
    				$deviceString = $deviceString . ' ' . $device['deviceName'] . ' ' . $device['deviceList'] . '\n';
    			}
    			$result['message'] = $deviceString;
    		}
    		
    	}
    
	    return new JsonModel($result);
    }

    public function deviceListAjaxAction(){
    
    	$result = array('status' => 'error', 'message' => 'There was some error. Try again.', 'chartData' => '');
    
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    
    	if($request->isXmlHttpRequest()){
    
    		$data = $request->getPost();
    
    		if(isset($data['deviceVal']) && !empty($data['deviceVal'])){
    			$devices = $data['deviceVal'];
    			$deviceList = array();
    			foreach ($devices as $device){
    				$devlist_e = explode(', ', $device['deviceList']);
    				//var_dump($devlist_e);
    				if (count($devlist_e) > 1)  {
	    				$devlist = array_slice($devlist_e, 0, count($devlist_e)-1 );
    				}
    				else {
	    				$devlist = array('dummy');
    				}
    				$deviceList[$device['deviceName']] = $devlist;
    			}
    		}
    		//Debug::dump($deviceList);
    		$device_o = new Device();
    		$device_o->setDeviceList($deviceList);
    		
    		// to create a query string
    		$device_string = $device_o->device_query_string();
    		$sql = $device_o->create_query_string_all($device_string);

    		// redmine database query
    		$query_resultSet = $this->getDeviceTable()->getSelectedDevicesIssues($sql); 
    		$query_resultSet->buffer(); 
    		
    		// process query data for charts
    		$resultA = $device_o->get_issues_by_device_all($query_resultSet);
    		$resultB = $device_o->get_issues_by_type_per_device_all($query_resultSet);
    		$resultC = $device_o->get_issues_by_type_per_app_all($query_resultSet);


    		//Debug::dump($resultbyType);
    		$headData = array('Subject', 'App', 'Devices', 'PLM # ', 'Redmine #', 'Status', 'Type', 'Assigned', 'Created');
    		//$device_string = $this->getDeviceTable()->fetchAll(); 
	    	$result = array('status' => 'success', 
	    					'message' => 'no errors',
	    					'tableData' => array( 'headData' => $headData, 'issueData' => $query_resultSet->toArray()),
	    					'chartData' => array('A'=> $resultA, 'B' => $resultB, 'C' => $resultC)
	    					);
	    	//Debug::dump($resultA);
    		
    	}
    
	    return new JsonModel($result);
    }

}

