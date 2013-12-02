<?php
namespace Album\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ExlPrepsubmodValidator implements InputFilterAwareInterface
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
                                        'name' => 'uploadTmp',
                                        'filters' => array(
                                                        array('name' => 'StripTags'),
                                                        array('name' => 'StringTrim'),
                                                        array('name' => 'filerenameupload',
                                                                        'options' => array(
                                                                                        'target'    => './public/data/uploads/',
                                                                                        'overwrite' => true,
                                                                                        'use_upload_name' => true)),
                                        ),
                                        'validators' => array(
                                                        array(
                                                        'name' => 'File\Extension',
                                                        'options' => array('extension' => array('txt', 'text'))),
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

                        $this->inputFilter = $inputFilter;
                        
                }
                return $this->inputFilter;
        }
}