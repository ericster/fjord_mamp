<?php
namespace Album\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AppSets extends Fieldset implements InputFilterProviderInterface
{
	public function __construct()
	{
		parent::__construct('appset');

		$this->add(array(
				'name' => 'appName',
				'options' => array(
// 						'label' => 'App Name'
				),
				'attributes' => array(
						'required' => 'required',
        				'placeholder' => 'T-mobile TV',
        				'class' => 'appname input-normal'
				)
		));

		$this->add(array(
				'name' => 'regexPattern',
// 				'type' => 'Zend\Form\Element\Text',
				'options' => array(
// 						'label' => 'Search Pattern'
				),
				'attributes' => array(
						'required' => 'required',
        				'placeholder' => 'T-mobile TV, TV, Tmobile TV',
        				'class' => 'appregex input-xxlarge',
// 						'maxlength' => '255'
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
				)
		);
	}
}