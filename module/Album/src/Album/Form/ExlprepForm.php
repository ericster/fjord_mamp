<?php
namespace Album\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
// use Album\Form\AppFieldset;

class ExlprepForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('exldata');
        $this->setAttribute('method', 'post');
//         $this->setAttribute('class', 'form-horizontal');
//         $this->setAttribute('class', 'form-vertical');
//         $this->setAttribute('role', 'form');
//         $this->setAttribute( array(
//         		'method' => 'post',
// //         		'class'  => 'form-horizontal'
//         		'class'  => 'form-vertical'
//         ));

        $this->add(array(
        		'name' => 'taskName',
        		'type' => 'Zend\Form\Element\Text',
        		'attributes' => array(
        				'placeholder' => 'TMO_Garda_T399',
//         				'required' => 'required',
        		),
        		'options' => array(
//         				'label' => 'TMO TV',
        		),
        ));

        $this->add(array(
        		'type' => 'Zend\Form\Element\Collection',
        		'name' => 'searchTerm',
        		'options' => array(
        				'label' => 'Please input search term',
        				'count' => 1,
        				'should_create_template' => false,
        				'allow_add' => true,
        				'target_element' => array(
// 				        		'type' => 'Zend\Form\Element\Text',
				        		'name' => 'searchTerm',
        						'type' => 'Album\Form\AppSets',
// 				        		'options' => array(
// 				        				'label' => 'TMOTV',
// 				        		),
        				)
        		)
        ));
        
//         $this->add(array(
//         		'name' => 'urlExlDn',
//         		'type' => 'Zend\Form\Element\Url',
//         		'attributes' => array(
//         				'placeholder' => 'http://www.test.com',
//         				'required' => 'required',
//         		),
//         		'options' => array(
//         				'label' => 'Brokendown Excel',
//         		),
//         ));
        
//         $this->add(array(
//         		'name' => 'uploadTmp',
//         		'type' => 'file',
// //         		'attributes' => array(
// //         				'required' => 'required',
// //         		),
//         		'options' => array(
// //         				'label' => 'Search Template Upload',
//         		),
//         ));
        
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
        
//         $this->add(array(
//         		'name' => 'csrf',
//         		'type' => 'Zend\Form\Element\Csrf',
//         ));

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