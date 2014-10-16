<?php

namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Test\Form\DeviceMapValidator;
use Test\Form\DeviceMapForm;

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

	public function issueAction()
	{
		return new ViewModel(array(
				'issues' => $this->getIssueTable()->fetchAll(),
// 				'devices' => $this->getIssueTable()->fetchAll(),
		));
	}
    public function getissuesAction()
    {
        return new ViewModel();
    }


}

