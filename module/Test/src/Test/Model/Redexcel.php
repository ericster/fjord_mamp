<?php
namespace Test\Model;

use Zend\Debug\Debug;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Font;
use PHPExcel_Style_Color;
use PHPExcel_IOFactory;

class Redexcel 
{
	public $resultSet;
	public $deviceList;
	
	public static $type = array("Crash","Functional", "Usability", "By Design", "UX Flow", "ANR", "Stability", "Automation");
	public static $priority = array("C","B","A");
	public static $tabname = "Summary";

	public function __construct($resultSet = null, $devicelist = null)
	{
		$this->resultSet = $resultSet;
		$this->deviceList = $devicelist;
	}

	public function main(){
		$excel = new PHPExcel();
		$excel->getProperties()->setCreator("RedExcel Team")->setTitle("Redmine Data Mining");
		
		$this->generate_summary($this->get_sheet($excel, 0));
		$sheet1 = $this->get_sheet($excel, 1);
		$sheet1->setTitle("Total Issues ");
		$this->generate_issues($sheet1, $this->get_data_issues());
		$this->get_sheet($excel, 0);		// set summary as the first page
		
		$fname = $this->create_output($excel);
		
		return $fname;
	}
	
	function create_output($excel) {
		// Save Excel 2007 file
		$fname = 'RedExcel-TAed-'. date("M-d-y-a"). ".xlsx";
		//echo $fname . "\n";
		//echo date("H:i:s") , " Write to Excel2007 format" , PHP_EOL;
		$writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
		$writer->setIncludeCharts(TRUE);
		$writer->save('public/data/' . $fname);
		//echo date("H:i:s") , " File written to " , $fname , PHP_EOL;
	
		//echo date("H:i:s") , " Done writing file" , PHP_EOL;
		//echo "File has been created in " , getcwd() , PHP_EOL;
	
		return $fname;
	}
	
	function get_sheet($excel, $page) {
		$sheets = array();
		$sheets[1] = true;
		if (!isset($sheets[$page]) || !$sheets[$page]) {
			$excel->createSheet(NULL, $page);
			$sheets[$page] = true;
		}
		$excel->setActiveSheetIndex($page);
	
		return $excel->getActiveSheet();
	}
	
	function get_data_issues() {
		$result = $this->resultSet;
	
		$issues_array = array();

		foreach($result->toArray() as $row){
			$array = array_values($row);
	
			$issues_array[] = $array;
		}
		return $issues_array;
	}
	
	function cmp($a, $b)
	{
		$suma = 0;
		$sumb = 0;
		foreach($a as $key => $avalue )
		{
			if($key != ''){
				$suma += $avalue;
			}
		}
		foreach($b as $key => $bvalue )
		{
			if($key != ''){
				$sumb += $bvalue;
			}
		}
	
		return $sumb - $suma;
	}
	
	function get_issues_by_type_per_app_all(){
		$type = self::$type;
		$result = $this->resultSet;
	
		$app = array();
		foreach($result as $row){
			if(!array_key_exists($row['app'], $app)) {
				$app[$row['app']] = array_fill_keys($type, 0);
			}
	
			$app[$row['app']][$row['issue_type']] = $app[$row['app']][$row['issue_type']] + 1;
		}
	
		uasort($app, array($this, 'cmp'));
		$result = array();
		$result[] = array_merge((array)'', $type);
		foreach (array_keys($app) as $val) {
			$result[] = array_merge((array)$val, array_values($app[$val]));
		}
	
		return $result;
	}

	function get_issues_by_priority_per_app_all(){
		$priority = self::$priority;
		$result = $this->resultSet;
	
		$app = array();
		foreach($result as $row){
			if(!array_key_exists($row['app'], $app)) {
				$app[$row['app']] = array_fill_keys($priority, 0);
			}
	
			$app[$row['app']][$row['plm_priority']] = $app[$row['app']][$row['plm_priority']] + 1;
		}
	
		uasort($app, array($this, 'cmp'));
		$result = array();
		$result[] = array_merge((array)'', $priority);
		foreach (array_keys($app) as $val) {
			$result[] = array_merge((array)$val, array_values($app[$val]));
		}
	
		return $result;
	}
	
	
	// removed pending(14) issues
	function get_issues_by_type_per_device_all(){
		$type = self::$type;
		$result = $this->resultSet;
		$deviceList = $this->deviceList;
	
		$devices = array();
		foreach($result as $row){
			$devices_array = split(';', $row['devices']);
			foreach($devices_array as $device){
				$device = trim($device);
	
				foreach (array_keys($deviceList) as $device_name){
					if (in_array($device, $deviceList[$device_name]))
						$device_rep = $device_name;
				}
				if(!array_key_exists($device_rep, $devices)) {
					$devices[$device_rep] = array_fill_keys($type, 0);
				}
	
				$devices[$device_rep][$row['issue_type']] = $devices[$device_rep][$row['issue_type']] + 1;
			}
		}
	
		//     arsort($devices);
		uasort($devices, array($this, 'cmp'));
		$result = array();
		$result[] = array_merge((array)'', $type);
		foreach (array_keys($devices) as $val) {
			$result[] = array_merge((array)$val, array_values($devices[$val]));
		}
	
		return $result;
	}

	function get_issues_by_priority_per_device_all(){
		$priority = self::$priority;
		$result = $this->resultSet;
		$deviceList = $this->deviceList;
	
		$devices = array();
		foreach($result as $row){
			$devices_array = split(';', $row['devices']);
			foreach($devices_array as $device){
				$device = trim($device);
	
				foreach (array_keys($deviceList) as $device_name){
					if (in_array($device, $deviceList[$device_name]))
						$device_rep = $device_name;
				}
				if(!array_key_exists($device_rep, $devices)) {
					$devices[$device_rep] = array_fill_keys($priority, 0);
				}
	
				$devices[$device_rep][$row['plm_priority']] = $devices[$device_rep][$row['plm_priority']] + 1;
			}
		}
	
		//     arsort($devices);
		uasort($devices, array($this, 'cmp'));
		$result = array();
		$result[] = array_merge((array)'', $priority);
		foreach (array_keys($devices) as $val) {
			$result[] = array_merge((array)$val, array_values($devices[$val]));
		}
	
		return $result;
	}
	
	function get_issues_by_device_all(){
		$result = $this->resultSet;
		$deviceList = $this->deviceList;
	
		$devices = array();
		foreach($result as $row){
			$devices_array = split(';', $row['devices']);
			foreach($devices_array as $device){
				$device = trim($device);
	
				foreach (array_keys($deviceList) as $device_name){
					if (in_array($device, $deviceList[$device_name]))
						$device_rep = $device_name;
				}
				if(!array_key_exists($device_rep, $devices)) {
					$devices[$device_rep] = 0;
				}
	
				$devices[$device_rep] = $devices[$device_rep] + 1;
			}
		}
	
		arsort($devices);
		$result = array();
		$result[] = array_merge((array)'', (array)'All Issues');
		foreach (array_keys($devices) as $val) {
			$result[] = array_merge((array)$val, (array)$devices[$val]);
		}
	
		return $result;
	}
	
	function get_issues_by_devices_per_app_all(){
		$result = $this->resultSet;
		//$devices = $this->get_issues_by_device_all();	
		$deviceList = $this->deviceList;
		$app = array();
		foreach($result as $row){
			$devices_array = split(';', $row['devices']);
			foreach($devices_array as $device){
				// app array
				if(!array_key_exists($row['app'], $app)) {
					$app[$row['app']] = array_fill_keys(array_keys($deviceList), 0);
				}

				$device = trim($device);
			
				foreach (array_keys($deviceList) as $device_name){
					if (in_array($device, $deviceList[$device_name]))
						$device_rep = $device_name;
				}

				$app[$row['app']][$device_rep] = $app[$row['app']][$device_rep] + 1;
			}
		}
	
		uasort($app, array($this, 'cmp'));
		$result = array();
		$result[] = array_merge((array)'', array_keys($deviceList));
		foreach (array_keys($app) as $val) {
			$result[] = array_merge((array)$val, array_values($app[$val]));
		}
	
		return $result;
	}
	
	function generate_issues($tab, $data) {
	
		// 	$tab->setTitle("Total Issues");
	
		$row_no = count($data);
		$col_no = count($data[0]);
	
		// Heading Title
		$tab->setCellValue('A1', 'SUBJECT');
		$tab->setCellValue('B1', 'PROJECT');
		$tab->setCellValue('C1', 'DEVICES');
		$tab->setCellValue('D1', 'PLM ID');
		$tab->setCellValue('E1', 'REDMINE ID');
		$tab->setCellValue('F1', 'STATUS');
		$tab->setCellValue('G1', 'ISSUE TYPE');
		$tab->setCellValue('H1', 'AUTHOR EMAIL');
		$tab->setCellValue('I1', 'ASSIGNEE EMAIL');
		$tab->setCellValue('J1', 'CREATED');
		$tab->setCellValue('K1', 'RESOLVED');
		$tab->setCellValue('L1', 'PENDING DAYS');
	
		// Heading Row/Column size
		foreach(range('A','C') as $columnID) {
			$tab->getColumnDimension($columnID)->setAutoSize(true);
		}
		$tab->getColumnDimension('D')->setWidth(20);
		$tab->getColumnDimension('E')->setWidth(20);
		$tab->getColumnDimension('F')->setWidth(20);
		$tab->getColumnDimension('G')->setWidth(20);
	
		foreach(range('H','K') as $columnID) {
			$tab->getColumnDimension($columnID)->setAutoSize(true);
		}
	
		$tab->getColumnDimension('L')->setWidth(25);
	
	
		// Heading Style
		$tab->getStyle('A1:C1')->applyFromArray(
				array(
						'font'    => array(
								'bold'      => true,
								'size' 		=> 14,
								'name'		=> 'Calibri',
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						),
						'borders' => array(
								'outline'     => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
								)
						),
						'fill' => array(
								'type'       => PHPExcel_Style_Fill::FILL_SOLID,
								'color'		 => array('argb' => 'FF99CC00'),
						),
				)
		);
	
		$tab->getStyle('D1:L1')->applyFromArray(
				array(
						'font'    => array(
								'bold'      => true,
								'size' 		=> 14,
								'name'		=> 'Calibri',
						),
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						),
						'borders' => array(
								'outline'     => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
								)
						),
						'fill' => array(
								'type'       => PHPExcel_Style_Fill::FILL_SOLID,
								'color'		 => array('argb' => 'FF99CC00'),
						),
				)
		);
	
		// Heading cells border
		$header_row = 1;
		$lastColumn = $tab->getHighestColumn();
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$tab->getStyle($column.$header_row)->applyFromArray(
					array(
							'borders' => array(
									'outline'     => array(
											'style' => PHPExcel_Style_Border::BORDER_THIN
									)
							)
					)
			);
		}
	
	
		// Column alignment
		$tab->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$tab->getStyle('L')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	
		//  to fill in issue data
		for ($i=0; $i < $row_no; $i++){
			for ($j=0; $j < $col_no; $j++) {
				// column: zero base, row: one base
				$val = $data[$i][$j];
				$col = $j;
				$row = $i+2;
				$tab->setCellValueByColumnAndRow($col, $row, $val);
			}
		}
	
		// Redmine Hyperlink
		$column = 'E';
		$lastRow = $tab->getHighestRow();
		for ($row = 2; $row <= $lastRow; $row++) {
			$cell = $tab->getCell($column.$row);
			$new_val = end(explode('/', $cell->getValue()));
			$url = $cell->getValue();
			$cell->getHyperlink($new_val)->setUrl($url);
			$cell->setValue('#'.$new_val);
			$columnRow = $column.$row;
	
			//Hyperlink indication: color and underline
			$tab->getStyle($columnRow)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$tab->getStyle($columnRow)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
		}
	
		// PLM Hyperlink
		$column = 'D';
		$PLM_url = 'http://splm.sec.samsung.net/wl/tqm/defect/defectsol/getDefectSolView.do?defectCode=';
		$lastRow = $tab->getHighestRow();
		for ($row = 2; $row <= $lastRow; $row++) {
			$cell = $tab->getCell($column.$row);
			$url = $PLM_url.$cell->getValue();
			$cell->getHyperlink($new_val)->setUrl($url);
			$columnRow = $column.$row;
	
			//Hyperlink indication: color and underline
			$tab->getStyle($columnRow)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$tab->getStyle($columnRow)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
		}
	
	
		// AutoFilter enabled
		$tab->setAutoFilter($tab->calculateWorksheetDimension());
	
	}

	function _get_range_x($icol, $start, $count) {
		$col = PHPExcel_Cell::stringFromColumnIndex($icol);
		return $col . $start . ":" . $col . ($start + $count - 2);
	}
	
	function _get_bar_chart($tab, $tabname, $title, $data, $cell_data, $cell_top_left, $cell_bottom_right) {
		$tab->fromArray($data, null, $cell_data);
		$cell_data_coordinate = PHPExcel_Cell::coordinateFromString($cell_data);
		$cell_data_coordinate[0] = PHPExcel_Cell::columnIndexFromString($cell_data_coordinate[0]);
	
		$dataSeriesLabels = array();
		$dataSeriesValues = array();
		for ($i = 1; $i < count($data[0]); ++$i) {
			$pos = PHPExcel_Cell::stringFromColumnIndex($cell_data_coordinate[0] + $i - 1) . $cell_data_coordinate[1];
			$dataSeriesLabels[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("String",
					$tabname . "!" . $pos, NULL, 1);
			$dataSeriesValues[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("Number",
					$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
						NULL, count($data)),
	
		);
	
		$series = new PHPExcel_Chart_DataSeries(
				PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
				PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,		// plotGrouping
				range(0, count($dataSeriesValues)-1),			// plotOrder
				$dataSeriesLabels,					// plotLabel
				$xAxisTickValues,					// plotCategory
				$dataSeriesValues					// plotValues
		);
	
		$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);
		$chart = new PHPExcel_Chart(
				$title,		// name
				new PHPExcel_Chart_Title($title),
				new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false),
				new PHPExcel_Chart_PlotArea(NULL, array($series)),
				true,			// plotVisibleOnly
				0,			// displayBlanksAs
				NULL,			// xAxisLabel
				NULL			// yAxisLabel
		);
	
		$chart->setTopLeftPosition($cell_top_left);
		$chart->setBottomRightPosition($cell_bottom_right);
		return $chart;
	}
	
	function _get_stacked_chart($tab, $tabname, $title, $data, $cell_data, $cell_top_left, $cell_bottom_right) {
		$tab->fromArray($data, null, $cell_data);
		$cell_data_coordinate = PHPExcel_Cell::coordinateFromString($cell_data);
		$cell_data_coordinate[0] = PHPExcel_Cell::columnIndexFromString($cell_data_coordinate[0]);
	
		$dataSeriesLabels = array();
		$dataSeriesValues = array();
		for ($i = 1; $i < count($data[0]); ++$i) {
			$pos = PHPExcel_Cell::stringFromColumnIndex($cell_data_coordinate[0] + $i - 1) . $cell_data_coordinate[1];
			$dataSeriesLabels[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("String",
					$tabname . "!" . $pos, NULL, 1);
			$dataSeriesValues[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("Number",
					$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
						NULL, count($data)),
		);
	
		$series = new PHPExcel_Chart_DataSeries(
				PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
				PHPExcel_Chart_DataSeries::GROUPING_STACKED,		// plotGrouping
				range(0, count($dataSeriesValues)-1),			// plotOrder
				$dataSeriesLabels,					// plotLabel
				$xAxisTickValues,					// plotCategory
				$dataSeriesValues					// plotValues
		);
	
		$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
		$chart = new PHPExcel_Chart(
				$title,		// name
				new PHPExcel_Chart_Title($title),
				new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false),
				new PHPExcel_Chart_PlotArea(NULL, array($series)),
				true,			// plotVisibleOnly
				0,			// displayBlanksAs
				NULL,			// xAxisLabel
				NULL			// yAxisLabel
		);
	
		$chart->setTopLeftPosition($cell_top_left);
		$chart->setBottomRightPosition($cell_bottom_right);
		return $chart;
	}
	
	
	function _get_pie_chart($tab, $tabname, $title, $data, $cell_data, $cell_top_left, $cell_bottom_right) {
		$tab->fromArray($data, null, $cell_data);
		$cell_data_coordinate = PHPExcel_Cell::coordinateFromString($cell_data);
		$cell_data_coordinate[0] = PHPExcel_Cell::columnIndexFromString($cell_data_coordinate[0]);

		$pos = PHPExcel_Cell::stringFromColumnIndex($cell_data_coordinate[0]) . $cell_data_coordinate[1];
		//	Set the Labels for each data series we want to plot
		//		Datatype
		//		Cell reference for data
		//		Format Code
		//		Number of datapoints in series
		//		Data values
		//		Data Marker
		$dataSeriesLabels = array( 
				new PHPExcel_Chart_DataSeriesValues("String", $tabname . "!" . $pos, NULL, 1),
		);

		//	Set the Data values for each data series we want to plot
		//		Datatype
		//		Cell reference for data
		//		Format Code
		//		Number of datapoints in series
		//		Data values
		//		Data Marker
		$dataSeriesValues = array( new PHPExcel_Chart_DataSeriesValues("Number",
				$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0], $cell_data_coordinate[1] + 1, count($data)),
				NULL, count($data)),
		);
	
		//	Set the X-Axis Labels
		//		Datatype
		//		Cell reference for data
		//		Format Code
		//		Number of datapoints in series
		//		Data values
		//		Data Marker
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
						NULL, count($data)),
		);
	
		$series = new PHPExcel_Chart_DataSeries(
				PHPExcel_Chart_DataSeries::TYPE_PIECHART,		// plotType
				PHPExcel_Chart_DataSeries::GROUPING_STANDARD,		// plotGrouping
				range(0, count($dataSeriesValues)-1),			// plotOrder
				$dataSeriesLabels,					// plotLabel
				$xAxisTickValues,					// plotCategory
				$dataSeriesValues					// plotValues
		);
		
		//	Set up a layout object for the Pie chart
		$layout = new PHPExcel_Chart_Layout();
		$layout->setShowVal(TRUE);
		$layout->setShowPercent(TRUE);
		
		$plotarea = new PHPExcel_Chart_PlotArea($layout, array($series));
	
		$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
		$chart = new PHPExcel_Chart(
				$title,		// name
				new PHPExcel_Chart_Title($title),
				new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false),
				// Unknown error from PHPExcel!!!
				//new PHPExcel_Chart_PlotArea(layout1, array($series1)),
				$plotarea,
				true,			// plotVisibleOnly
				0,			// displayBlanksAs
				NULL,			// xAxisLabel
				NULL			// yAxisLabel
		);
	
		$chart->setTopLeftPosition($cell_top_left);
		$chart->setBottomRightPosition($cell_bottom_right);
		return $chart;
	}
	
	function _get_pie_chart_test($tab, $tabname, $title, $data, $cell_data, $cell_top_left, $cell_bottom_right) {

		// A1100
		$tab->fromArray(
				array(
						array('',	2010,	2011,	2012),
						array('Q1',   12,   15,		21),
						array('Q2',   56,   73,		86),
						array('Q3',   52,   61,		69),
						array('Q4',   30,   32,		0),
				), null, $cell_data
		);
	
		
		
		$dataseriesLabels1 = array(
				new PHPExcel_Chart_DataSeriesValues('String', $tabname . "!" . '$C$1101', NULL, 1),	//	2011
		);
		$xAxisTickValues1 = array(
				new PHPExcel_Chart_DataSeriesValues('String', $tabname . "!" . '$A$1102:$A$1105', NULL, 4),	//	Q1 to Q4
		);
		$dataSeriesValues1 = array(
				new PHPExcel_Chart_DataSeriesValues('Number', $tabname . "!" . '$C$1102:$C$1105', NULL, 4),
		);
		
		//	Build the dataseries
		$series1 = new PHPExcel_Chart_DataSeries(
				PHPExcel_Chart_DataSeries::TYPE_PIECHART,				// plotType
				PHPExcel_Chart_DataSeries::GROUPING_STANDARD,			// plotGrouping
				range(0, count($dataSeriesValues1)-1),					// plotOrder
				$dataseriesLabels1,										// plotLabel
				$xAxisTickValues1,										// plotCategory
				$dataSeriesValues1										// plotValues
		);
		
		//	Set up a layout object for the Pie chart
		$layout1 = new PHPExcel_Chart_Layout();
		$layout1->setShowVal(TRUE);
		$layout1->setShowPercent(TRUE);
		
		$plotarea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
	
		//$series1->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
		$chart = new PHPExcel_Chart(
				$title,		// name
				new PHPExcel_Chart_Title($title),
				new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false),
				// Unknown error from PHPExcel!!!
				//new PHPExcel_Chart_PlotArea(layout1, array($series1)),
				$plotarea1,
				true,			// plotVisibleOnly
				0,			// displayBlanksAs
				NULL,			// xAxisLabel
				NULL			// yAxisLabel
		);
	
		$chart->setTopLeftPosition($cell_top_left);
		$chart->setBottomRightPosition($cell_bottom_right);
		return $chart;
	}
	
	
	function _get_stacked_chart_option($tab, $tabname, $title, $data, $cell_data, $cell_top_left, $cell_bottom_right) {
		$tab->fromArray($data, null, $cell_data);
		$cell_data_coordinate = PHPExcel_Cell::coordinateFromString($cell_data);
		$cell_data_coordinate[0] = PHPExcel_Cell::columnIndexFromString($cell_data_coordinate[0]);
	
		$dataSeriesLabels = array();
		$dataSeriesValues = array();
		for ($i = 1; $i < count($data[0]); ++$i) {
			$pos = PHPExcel_Cell::stringFromColumnIndex($cell_data_coordinate[0] + $i - 1) . $cell_data_coordinate[1];
			$dataSeriesLabels[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("String",
					$tabname . "!" . $pos, NULL, 1);
			$dataSeriesValues[($i - 1)] = new PHPExcel_Chart_DataSeriesValues("Number",
					$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . $this->_get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
						NULL, count($data)),
		);
	
		$series = new PHPExcel_Chart_DataSeries(
				PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
				PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,		// plotGrouping
				range(0, count($dataSeriesValues)-1),			// plotOrder
				$dataSeriesLabels,					// plotLabel
				$xAxisTickValues,					// plotCategory
				$dataSeriesValues					// plotValues
		);
	
		$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
		$chart = new PHPExcel_Chart(
				$title,		// name
				new PHPExcel_Chart_Title($title),
				new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false),
				new PHPExcel_Chart_PlotArea(NULL, array($series)),
				true,			// plotVisibleOnly
				0,			// displayBlanksAs
				NULL,			// xAxisLabel
				NULL			// yAxisLabel
		);
	
		$chart->setTopLeftPosition($cell_top_left);
		$chart->setBottomRightPosition($cell_bottom_right);
		return $chart;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	function generate_summary($tab) {
		$tabname = self::$tabname;
	
		$tab->setTitle($tabname);
	
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "All Issues per Device", $this->get_issues_by_device_all(),  "A600", "A2", "H18"));
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "All Issues per App", $this->get_issues_by_devices_per_app_all(), 		"A900", "A22", "H38"));
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "Issue Type per Device", $this->get_issues_by_type_per_device_all(),  "A400", "I2", "P18"));
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "Issue Priority per Device", $this->get_issues_by_priority_per_device_all(),  "A700", "I22", "P38"));
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "Issue Type per App", $this->get_issues_by_type_per_app_all(), 		"A500", "Q2", "X18"));
		$tab->addChart($this->_get_stacked_chart($tab, $tabname, "Issue Priority per App", $this->get_issues_by_priority_per_app_all(), 		"A800", "Q22", "X38"));
		$tab->addChart($this->_get_pie_chart($tab, $tabname, "Issue per App pie chart", $this->get_issues_by_devices_per_app_all(), 		"A1101", "A42", "H58"));
	
	}
	
	
}