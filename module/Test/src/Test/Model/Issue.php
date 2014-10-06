<?php

// module/Test/src/Test/Model/Album.php:
namespace Test\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Issue implements InputFilterAwareInterface
{
    public $id;
    public $subject;
    public $app;
    public $devices;
    public $plm;
    public $url;
    public $type;
    public $status;
    public $assignee;
    public $created;
    public $pending_days;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id']))     ? $data['id']     : null;
        $this->subject = (isset($data['subject'])) ? $data['subject'] : null;
        $this->app  = (isset($data['app']))  ? $data['app']  : null;
        $this->devices  = (isset($data['devices']))  ? $data['devices']  : null;
        $this->plm  = (isset($data['plm']))  ? $data['plm']  : null;
        $this->url  = (isset($data['url']))  ? $data['url']  : null;
        $this->type  = (isset($data['type']))  ? $data['type']  : null;
        $this->status  = (isset($data['status']))  ? $data['status']  : null;
        $this->assignee  = (isset($data['assignee']))  ? $data['assignee']  : null;
        $this->created_on  = (isset($data['created_on']))  ? $data['created_on']  : null;
        $this->pending_days  = (isset($data['pending_days']))  ? $data['pending_days']  : null;
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