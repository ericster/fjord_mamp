<?php
namespace Album\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AppSets extends Fieldset implements InputFilterProviderInterface
{
	public function __construct()
	{
		parent::__construct('deviceset');

		$this->add(array(
				'name' => 'deviceName',
				'options' => array(
// 						'label' => 'Device Name'
				),
				'attributes' => array(
						'required' => 'required',
        				'placeholder' => 'TR ATT',
        				'class' => 'devicename input-normal'
				)
		));

		$this->add(array(
				'name' => 'deviceList',
				'options' => array(
// 						'label' => 'Device List autocomplete'
				),
				'attributes' => array(
						'required' => 'required',
        				'class' => 'deviceList input-xxlarge',
				)
		));

	}

	/**
	 * @return array
	 */
	// Break point: don't know why. safe to true to false
	public function getInputFilterSpecification()
	{
		return array(
					'name' => array(
					'required' => false,
							),
				/*
				'appName' => array(
						'filters' => array(
								array('name' => 'StripTags'),
								array('name' => 'StringTrim'),
						),
		                'validators' => array(
// 			                    array(
// 			                        'name' => 'Float')
		                ),
				),	
				'regexPattern' => array(
						'filters' => array(
								array('name' => 'StripTags'),
								array('name' => 'StringTrim'),
						),
		                'validators' => array(
// 			                    array(
// 			                        'name' => 'Float')
		                )

				)
				*/
		);
	}
}