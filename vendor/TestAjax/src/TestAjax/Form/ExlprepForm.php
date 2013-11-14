<?php
namespace TestAjax\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class ExlprepForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('brige');
        $this->setAttribute('method', 'post');

        $this->add(array(
        		'name' => 'TmoTV',
        		'type' => 'Zend\Form\Element\Text',
        		'attributes' => array(
        				'placeholder' => 'Type something...',
        				'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'TMO TV',
        		),
        ));

        /*
        $this->add(array(
        		'type' => 'Zend\Form\Element\Collection',
        		'name' => 'searchTemplate',
        		'options' => array(
        				'label' => 'Please choose categories for this product',
        				'count' => 1,
        				'should_create_template' => true,
        				'allow_add' => true,
        				'target_element' => array(
				        		'type' => 'Zend\Form\Element\Text',
//         						'type' => 'Application\Form\CategoryFieldset'
        				)
        		)
        ));
        
        $this->add(array(
        		'name' => 'url',
        		'type' => 'Zend\Form\Element\Url',
        		'attributes' => array(
        				'placeholder' => 'http://www.test.com',
        				'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'Text',
        		),
        ));
        
        $this->add(array(
        		'name' => 'upload',
        		'type' => 'file',
        		'attributes' => array(
        				'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'File Upload',
        		),
        ));
        
        $this->add(array(
        		'name' => 'upload',
        		'type' => 'file',
        		'attributes' => array(
        				'required' => 'required',
        		),
        		'options' => array(
        				'label' => 'File Upload',
        		),
        ));
        
        $this->add(array(
        		'name' => 'csrf',
        		'type' => 'Zend\Form\Element\Csrf',
        ));
        */

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
            ),
        ));
    }
}