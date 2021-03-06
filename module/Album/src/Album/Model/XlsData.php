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
    public $casecode_no = 0;
    public $headrow_no= 0;
    public static $searchCells = array("Title", "Problem", "Reproduction Route", "Cause", "Countermeasure");
    public static $visibleCells = array("Case Code", "Title", "Problem", "Reproduction Route", "Cause", "Countermeasure");
    public static $casecode = "Case Code";

	
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
    


    function read_rows_for_heading()
    {
    	 
//     	$inputFileName = './public/data/uploads/Garda_issues_1015.xls';
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
    	/*
    	 * safe asssumption that heading is within 10th row
    	 */
    	$highestRow = 10;
    	$highestColumn = $sheet->getHighestColumn();
    
    	//  Loop through each row of the worksheet in turn
    	for ($row = 1; $row <= $highestRow; $row++){
    		//  Read a row of data into an array
    		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
    				NULL,
    				TRUE,
    				FALSE);
    		//  Insert row data array into your database of choice here
//     		print_r("row_data_inserted");
// 	    	var_dump($rowData);
	    	// debugged : one row is an array of an array of cells
    		array_push($this->row_arr, $rowData[0]);
    	}
    
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
//     		print_r("row_data_inserted");
// 	    	var_dump($rowData);
	    	// debugged : one row is an array of an array of cells
    		array_push($this->row_arr, $rowData[0]);
    	}
    
    }
    
    /*
     * find heading row with reasonable assumptions
     * 1. heading row has a "Case Code" cell or other keywords such as Reproduction Route/Countermeasure
     * 2. heading row can be found withn 10 rows. 
     */
    function set_heading_row_no(){
    	$keywords = array("Case Code",);
    	$headingRow = 0;
    	foreach ($this->row_arr as $key => $row) {
//     		print_r($key);
			if($key < 10) {
				foreach($keywords as $keyword) {
		    		if(in_array($keyword, $row)){
// 		    			print_r("heading row");
		    			$headingRow = $key;
		//     			print_r($row);
		    		}
				}

			}
    	} 
    	$this->headrow_no = $headingRow;
    }
    function get_heading_row_no(){
    	return $this->headrow_no;

    }
    
    function get_heading_row(){
    	$this->set_heading_row_no();
    	$heading_row_no = $this->headrow_no;
    	return $this->row_arr[$heading_row_no];
    }
    
    function add_casecode_cell($traverseCells, $casecodeCell) {
    	$visible_cells = $traverseCells;
    	if(!in_array($casecodeCell, $traverseCells)){
    		array_push($visible_cells, $casecodeCell);
    	}
    	return $visible_cells;
    }
    function get_visible_headings($visible_cells){
    	$heading = $this->get_heading_row();
		$head_cells = array();
    	foreach ($heading as $key => $cell) {
    		if(in_array($key,$visible_cells)){
    			array_push($head_cells, $cell);
    		} 
    	}
    	return $head_cells;
	}

    function get_search_cells(){
    	$heading = $this->get_heading_row();
		$searchCells = self::$searchCells;
		$checkCells = array();
    	foreach ($heading as $key => $cell) {
    		if(in_array($cell,$searchCells)){
    			array_push($checkCells, $key);
    		} 
    	}
    	return $checkCells;
	}
	
	function get_casecode_cell(){
				return $this->casecode_no;
	}

	function set_casecode_cell(){
		$heading = $this->get_heading_row();
		foreach ($heading as $key => $cell) {
			if($cell == self::$casecode){
				$this->casecode_no = $key;
			}
		}
	}
	function get_visible_cells(){
		$heading = $this->get_heading_row();
		$visibleCells = self::$visibleCells;
		$cellsToShow = array();
		foreach ($heading as $key => $cell) {
			if(in_array($cell,$visibleCells)){
				array_push($cellsToShow, $key);
			}
		}
		return $cellsToShow;
	}
    
    function not_classified_rows($no_class_list) {
//     	print_r("ready to cull out");
//     	print_r($no_class_list);
    	$no_class_rows = array();
//     	print_r("size of row_arr: ");
//     	print_r(sizeof($this->row_arr));
//     	var_dump($this->row_arr);

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
    
    function get_selected_cols($sel_cols, $row_arr) {
//     	print_r("row_arr = ");
//     	print_r($row_arr);
//     	print_r("sel_cols : ". implode(' ', $sel_cols));
    	$mod_row_arr = array();
    	foreach($row_arr as $rowNo => $row){
//     		print_r("row = ");
//     		print_r($row);
			$headNo = $this->headrow_no;
// 			$headNo = -1;
			if($rowNo > $headNo){
	    		$mod_row = array();
	//     		print_r("row size is :". sizeof($row));
	    		foreach ($row as $idx => $col){
	    			if(in_array($idx, $sel_cols)){
	    				array_push($mod_row,$col );
	    			}
	    		}
	    		array_push($mod_row_arr, $mod_row);
			}
    	}
    	
    	return $mod_row_arr;
    }
    
    
    function read_cells(){
    	
//     	/** Error reporting */
//     	error_reporting(E_ALL);
//     	ini_set('display_errors', TRUE);
//     	ini_set('display_startup_errors', TRUE);
    	
    	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    	$inputFileName = './public/data/uploads/Garda_issues_1015.xls';
    	$inputFileName = $this->exlfile;
//     	print_r('reading exl file');
//     	print_r($inputFileName);
    	
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