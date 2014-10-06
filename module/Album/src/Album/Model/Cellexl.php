<?php

// module/Album/src/Album/Model/Album.php:
namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Cellexl implements InputFilterAwareInterface
{
    public $row_no;
    public $col_no;
    public $cell_value;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->row_no     = (isset($data['row_no']))     ? $data['row_no']     : null;
        $this->col_no = (isset($data['col_no'])) ? $data['col_no'] : null;
        $this->cell_value  = (isset($data['cell_value']))  ? $data['cell_value']  : null;
    }

	
 // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
}