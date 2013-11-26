<?php

// module/Album/src/Album/Model/Album.php:
namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

// class XlsData implements InputFilterAwareInterface
class XlsData 
{
    public $cell_arr = array();
    public $row_arr = array();
    public $exlfile = '';

	
	public function __construct($exlfile = ""){
		$this->exlfile = $exlfile;
	}
	
	public function getCellArr(){
		return $this->cell_arr;
	}
	public function getRowArr(){
		return $this->row_arr;
// 		return "getting row arrasy from ExlData";
	}

 // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
    

    function read_rows()
    {
    	 
    	$inputFileName = './public/data/uploads/Garda_issues_1015.xls';
    	$inputFileName = $this->exlfile;
//     	print_r('reading exl file');
//     	print_r($inputFileName);
    
    	//  Read your Excel workbook
    	try {
//     		var_dump($inputFileName);
    		$inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
//     		var_dump($inputFileType);
    		$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
    		$objPHPExcel = $objReader->load($inputFileName);
    	} catch(Exception $e) {
    		die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
    	}
    
    	//  Get worksheet dimensions
    	$sheet = $objPHPExcel->getSheet(0);
    	$highestRow = $sheet->getHighestRow();
    	$highestColumn = $sheet->getHighestColumn();
    
    	//  Loop through each row of the worksheet in turn
    	for ($row = 1; $row <= $highestRow; $row++){
    		//  Read a row of data into an array
    		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
    				NULL,
    				TRUE,
    				FALSE);
    		//  Insert row data array into your database of choice here
// 	    	print_r($rowData);
    		array_push($this->row_arr, $rowData);
    	}
    
    }
    
    function not_classified_rows($no_class_list) {
//     	print_r("ready to cull out");
//     	print_r($no_class_list);
    	$no_class_rows = array();
//     	print_r("size is : ");
//     	print_r(sizeof($this->row_arr));
    	foreach ($this->row_arr as $key => $row) {
//     		print_r($key);
    		if(in_array($key, $no_class_list)){
//     			print_r("matching");
//     			print_r($row);
    			array_push($no_class_rows, $row);
    		}
    	} 
//     	print_r("returning no_class_rows. size is : ");
//     	print_r(sizeof($no_class_rows));
    	return $no_class_rows;
    	
    }
    function read_cells(){
    	
//     	/** Error reporting */
//     	error_reporting(E_ALL);
//     	ini_set('display_errors', TRUE);
//     	ini_set('display_startup_errors', TRUE);
    	
    	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    	$inputFileName = './public/data/uploads/Garda_issues_1015.xls';
    	$inputFileName = $this->exlfile;
    	print_r('reading exl file');
    	print_r($inputFileName);
    	
    	//  Read your Excel workbook
    	try {
    		var_dump($inputFileName);
    		$inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
    		var_dump($inputFileType);
    		$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
    		$objPHPExcel = $objReader->load($inputFileName);
    	} catch(Exception $e) {
    		die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
    	}
    	
    	echo date('H:i:s') , " Iterate worksheets" , EOL;
    	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    		echo 'Worksheet - ' , $worksheet->getTitle() , EOL;
    	
    		foreach ($worksheet->getRowIterator() as $row) {
    			echo '    Row number - ' , $row->getRowIndex() , EOL;
    	
    			$cellIterator = $row->getCellIterator();
    			$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
    			foreach ($cellIterator as $cell) {
    				if (!is_null($cell)) {
    					echo '        Cell - ' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , EOL;
//     					array_push($this->cell_arr, $var)
    				}
    			}
    		}
    	}
    }
}