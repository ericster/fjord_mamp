<?php
namespace Album\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ExlPrepValidator implements InputFilterAwareInterface
{
	protected $inputFilter;

	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	public function getInputFilter()
	{
		if (!$this->inputFilter)
		{
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			$searchFilter = new InputFilter();


			$inputFilter->add($factory->createInput([
					'name' => 'taskName',
					'required' => true,
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
					),
					]));


			$inputFilter->add($factory->createInput([
					'name' => 'uploadExl',
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
							array('name' => 'filerenameupload',
// 							array('name' => 'File\RenameUpload',
								  'options' => array(
										'target'    => './public/data/uploads/',
										'overwrite' => true,
										'use_upload_name' => true)),
							),
					'validators' => array(
							array(
							'name' => 'File\Extension',
							'options' => array('extension' => array('xls', 'xlsx'))),
					),
					]));

			/*
			$searchFilter->add($factory->createInput([
					'name' => 'appName',
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
					),
					]));
			$searchFilter->add($factory->createInput([
					'name' => 'searchTerm',
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
					),
					]));
			
			$inputFilter->add($searchFilter, "searchTerm");
			*/
			
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}