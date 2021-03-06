<?php
namespace Album\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
// use Album\Form\AppFieldset;

class ExlprepsubmodForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('exldatasub');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
        		'name' => 'uploadTmp',
        		'type' => 'file',
//         		'attributes' => array(
//         				'required' => 'required',
//         		),
        		'options' => array(
//         				'label' => 'Search Template Upload',
        		),
        ));
        
        $this->add(array(
        		'name' => 'uploadExl',
        		'type' => 'file',
        		'attributes' => array(
        				'required' => 'required',
        		),
        		'options' => array(
        		//         				'label' => 'Excel Spreadsheet Upload',
        		),
        ));
        

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Take it',
                'id' => 'submitbutton',
                'class' => 'btn btn-sm',
            ),
        ));
    }
}