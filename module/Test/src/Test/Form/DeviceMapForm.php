<?php
namespace Album\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class ExlprepForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('devicemap');
        $this->setAttribute('method', 'post');

        $this->add(array(
        		'name' => 'taskName',
        		'type' => 'Zend\Form\Element\Text',
        		'attributes' => array(
        				'placeholder' => 'RedExcel Mining 3Q',
//         				'required' => 'required',
        		),
        		'options' => array(
        		),
        ));

        $this->add(array(
        		'type' => 'Zend\Form\Element\Collection',
        		'name' => 'deviceVal',
        		'options' => array(
        				'label' => 'Please input search term',
        				'count' => 1,
        				'should_create_template' => false,
        				'allow_add' => true,
        				'target_element' => array(
				        		'name' => 'deviceVal',
        						'type' => 'Test\Form\DeviceSet',
        				)
        		)
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }
}