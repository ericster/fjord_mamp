<?php

// module/Test/src/Test/Model/Album.php:
namespace Test\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Device implements InputFilterAwareInterface
{
    public $id;
    public $devices;

    protected $inputFilter;
    public $deviceList;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id']))     ? $data['id']     : null;
        $this->devices = (isset($data['devices'])) ? $data['devices'] : null;
    }

    /*
    	param: $devicelist = array(
    			'T ATT' => array('NDA Device', 'N910A T ATT'),
    			//'Chagall ATT' => array('T807A Chagall'),
    			//'KLIMT ATT' => array('T707A KLIMT'),
    	);
    */
    public function setDeviceList($devicelist)
    {
    	$this->deviceList = $devicelist;
    	
    }

    public function getDeviceList()
    {
    	return $this->deviceList; 
    	
    }
    
    public function device_query_string($deviceList) {
    	$device_string = ' (';
    	foreach (array_keys($deviceList) as $device_name){
    		foreach ($deviceList[$device_name] as $device) {
    			if ($device == end(end($deviceList))){
    				$Operator = '';
    			}
    			else {
    				$Operator = ' OR ';
    			}
    			$device_string = $device_string . 'cv.value = \'' . $device . '\'' . $Operator;
    		}
    	}
    	$device_string = $device_string . ' )';
    
    	return $device_string;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'artist',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
	
 // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
}