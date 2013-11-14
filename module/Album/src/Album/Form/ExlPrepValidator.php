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


			$inputFilter->add($factory->createInput([
					'name' => 'TmoTV',
					'required' => true,
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
					),
					]));


			$inputFilter->add($factory->createInput([
					'name' => 'uploadTmp',
					'filters' => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
							array('name' => 'filerenameupload',
									'options' => array(
// 											'target'    => '/usr/local/zend/apache2/htdocs/myapp/public/data/uploads/',
											'target'    => './public/data/uploads/',
											'overwrite' => true,
											'use_upload_name' => true)),
					),
					'validators' => array(
							array(
									'name' => 'File\Extension',
									'extension' => array('txt', ),
							)
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
// 										'target'    => '/usr/local/zend/apache2/htdocs/myapp/public/data/uploads/',
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
			
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}