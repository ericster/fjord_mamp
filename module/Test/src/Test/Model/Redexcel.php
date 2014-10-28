<?php
namespace Test\Model;

use Zend\Debug\Debug;

class Redexcel 
{
	public $device_string;
	public $deviceList;

	public function redexcelMain(){
		$excel = new PHPExcel();
		$excel->getProperties()->setCreator("RedExcel Team")->setTitle("Redmine Data Mining");
		
		generate_summary(get_sheet($excel, 0));
		$sheet1 = get_sheet($excel, 1);
		$sheet1->setTitle("Total Issues ");
		generate_issues($sheet1, get_data_issues());
		get_sheet($excel, 0);		// set summary as the first page
		
		$fname = create_output($excel);
	}
	
	function create_output($excel) {
		// Save Excel 2007 file
		$fname = 'RedExcel-TAed-'. date("M-d-y-a"). ".xlsx";
		echo $fname . "\n";
		echo date("H:i:s") , " Write to Excel2007 format" , PHP_EOL;
		$writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
		$writer->setIncludeCharts(TRUE);
		$writer->save($fname);
		echo date("H:i:s") , " File written to " , $fname , PHP_EOL;
	
	
		// Echo memory peak usage
		echo date("H:i:s") , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , PHP_EOL;
	
		// Echo done
		echo date("H:i:s") , " Done writing file" , PHP_EOL;
		echo "File has been created in " , getcwd() , PHP_EOL;
	
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

	/*
	$deviceList = array(
			'T ATT' => array('NDA Device', 'N910A T ATT'),
			'Chagall ATT' => array('T807A Chagall'),
			'KLIMT ATT' => array('T707A KLIMT'),
	);
	*/
	
	function device_query_string($deviceList) {
		$device_string = ' (';
		//echo $device_string . '\n';
		foreach (array_keys($deviceList) as $device_name){
			foreach ($deviceList[$device_name] as $device) {
				if ($device == end(end($deviceList))){
					$Operator = '';
				}
				else {
					$Operator = ' OR ';
				}
				$device_string = $device_string . 'cv.value = \'' . $device . '\'' . $Operator;
			}
		}
		$device_string = $device_string . ' )';
	
		return $device_string;
	
	}
	
	//$device_string = device_query_string($deviceList);
	
	/*
	$query_string_all =
	'select subject, app, cv.value as devices, plm, url, status, issue_type, author_mail, assignee_mail, created_on,
    case status
        when \'Resolved\' Then updated_on
        else null
    end as resolved_date,
    case status
        when \'Resolved\' Then datediff(updated_on, created_on)
        else datediff(curdate(), created_on)
    end as days
    from
    (
        select
            issues.id,
            issues.project_id,
            projects.name as app,
            issue_statuses.name as status,
            author.mail as author_mail, assignee.mail as assignee_mail,
            issues.subject, issues.created_on, issues.updated_on, issues.start_date,
            concat(\'http:\/\/redmine.telecom.sna.samsung.com/issues/\', issues.id) as url
        from
            issues, issue_statuses, projects, users as author, users as assignee
        where
            issues.status_id = issue_statuses.id and
            issues.project_id = projects.id and
            issues.author_id = author.id and
            issues.assigned_to_id = assignee.id
        order by
            projects.id, issues.created_on asc
    ) a
    join custom_values as cv on cv.customized_id = a.id
    left join
    (
        select issues.id, group_concat(value separator \'; \') as devices from issues, custom_values
        where
            issues.id = custom_values.customized_id and
            custom_values.custom_field_id = \'9\'
        group by custom_values.custom_field_id, custom_values.customized_id
    ) devices
    on a.id = devices.id
    left join
    (
        select issues.id, group_concat(value separator \'; \') as plm from issues, custom_values
        where
            issues.id = custom_values.customized_id and
            custom_values.custom_field_id = \'6\'
        group by custom_values.custom_field_id, custom_values.customized_id
    ) plm
    on a.id = plm.id
    left join
    (
        select issues.id, group_concat(value separator \'; \') as issue_type from  issues, custom_values
        where
            issues.id = custom_values.customized_id and
            custom_values.custom_field_id = \'32\'
        group by custom_values.custom_field_id, custom_values.customized_id
    ) issue_type
    on a.id = issue_type.id
    join
    (
        select p2.id
        from projects p
            left join projects as p1 on p1.parent_id = p.id
            left join projects as p2 on p2.parent_id = p1.id
        where
            p.id = 95
    ) vux_projects
    on a.project_id = vux_projects.id
	where cv.custom_field_id = \'9\' and ' .
		$device_string .
		'order by created_on desc';
	*/
	
	
	function get_all_issues_array($query){
		//    $con=mysqli_connect("wiki.telecom.sna.samsung.com","redmine_dev","","redmine_default");
		//    $con=mysqli_connect("105.59.102.16","redmine_dev","","redmine_default");
		$con=mysqli_connect("localhost","root","root","redmine_bak");
	
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error() . "\n";
		} else {
			echo "Connected to Database\n";
		}
	
		$set_db = mysqli_query($con, 'use redmine_default');
		$result = mysqli_query($con, $query);
	
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	
		return $result;
	}
	
	function get_data_issues() {
		global $query_string_all;
	
		$result = get_all_issues_array($query_string_all);
	
		$issues_array = array();
		while($row = mysqli_fetch_assoc($result)) {
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
		global $query_string;
		global $query_string_all;
	
		$result = get_all_issues_array(' select possible_values from custom_fields where id=32');
		//     $statuses = array();
		//    $type= mysqli_fetch_array($result, MYSQLI_NUM);
		//    $type_str = explode("--- ", $type[0])[1];
		//    $type_arr = explode("-", $type_str);
		//    $type = array_slice($type_arr, 1);
	
		$type = array("Crash","Functional", "Usability", "By Design", "UX Flow", "ANR", "Stability", "Automation");
	
		$result = get_all_issues_array($query_string_all);
	
		$app = array();
		while($row = mysqli_fetch_array($result)) {
			if(!array_key_exists($row['app'], $app)) {
				$app[$row['app']] = array_fill_keys($type, 0);
			}
	
			$app[$row['app']][$row['issue_type']] = $app[$row['app']][$row['issue_type']] + 1;
		}
	
		uasort($app, cmp);
		$result = array();
		$result[] = array_merge((array)'', $type);
		foreach (array_keys($app) as $val) {
			$result[] = array_merge((array)$val, array_values($app[$val]));
		}
	
		return $result;
	}
	
	
	// removed pending(14) issues
	function get_issues_by_type_per_device_all(){
		global $query_string;
		global $query_string_all;
		global $deviceList;
	
		$type = array("Crash","Functional", "Usability", "By Design", "UX Flow", "ANR", "Stability", "Automation");
	
		$result = get_all_issues_array($query_string_all);
	
		$devices = array();
		while($row = mysqli_fetch_array($result)) {
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
		uasort($devices, cmp);
		$result = array();
		$result[] = array_merge((array)'', $type);
		foreach (array_keys($devices) as $val) {
			$result[] = array_merge((array)$val, array_values($devices[$val]));
		}
	
		return $result;
	}
	
	function get_issues_by_device_all(){
		global $query_string;
		global $query_string_all;
		global $deviceList;
	
		$result = get_all_issues_array($query_string_all);
	
		$devices = array();
		while($row = mysqli_fetch_array($result)) {
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
	
	function generate_issues($tab, $data) {
	
		echo "Geneate issues on a table\n";
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
					$tabname . "!" . _get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . _get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
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
					$tabname . "!" . _get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . _get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
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
					$tabname . "!" . _get_range_x($cell_data_coordinate[0] + $i - 1, $cell_data_coordinate[1] + 1, count($data)),
					NULL, count($data));
		}
	
		$xAxisTickValues = array(
				new PHPExcel_Chart_DataSeriesValues('String',
						$tabname . "!" . _get_range_x($cell_data_coordinate[0] - 1, $cell_data_coordinate[1] + 1, count($data)),
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
		$tabname = "Summary";
	
		$tab->setTitle($tabname);
	
		$tab->addChart(_get_stacked_chart($tab, $tabname, "All Issues per Device", get_issues_by_device_all(),  "A600", "A2", "H18"));
		$tab->addChart(_get_stacked_chart($tab, $tabname, "Issue Type per Device", get_issues_by_type_per_device_all(),  "A400", "I2", "P18"));
		$tab->addChart(_get_stacked_chart($tab, $tabname, "Issue Type per App", get_issues_by_type_per_app_all(), 		"A500", "Q2", "X18"));
	
	}
	
	
}