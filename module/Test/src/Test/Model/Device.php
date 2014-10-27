<?php

// module/Test/src/Test/Model/Album.php:
namespace Test\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Debug\Debug;

class Device implements InputFilterAwareInterface
{
    public $id;
    public $devices;

    protected $inputFilter;
    public $deviceList;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id']))     ? $data['id']     : null;
        $this->devices = (isset($data['devices'])) ? $data['devices'] : null;
    }

    /*
    	param: $devicelist = array(
    			'T ATT' => array('NDA Device', 'N910A T ATT'),
    			//'Chagall ATT' => array('T807A Chagall'),
    			//'KLIMT ATT' => array('T707A KLIMT'),
    	);
    */
    public function setDeviceList($devicelist)
    {
    	/*
       	$devicelist_test = array(
    			'T ATT' => array('NDA Device', 'N910A T ATT'),
    			'Chagall ATT' => array('T807A Chagall'),
    			'KLIMT ATT' => array('T707A KLIMT'),
    	); 	
    	*/
       	$this->deviceList = $devicelist;
    	
    }

    public function getDeviceList()
    {
    	return $this->deviceList; 
    	
    }
    
    public function device_query_string() {
    	$deviceList = $this->deviceList;

    	//Debug::dump($deviceList);
    	$device_string = ' (';
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
    	//var_dump(($device_string));
    
    	return $device_string;
    }
    
    public function create_query_string_all($device_string){

    	$query_string_all = 'select a.id, subject, app, cv.value as devices, plm, issue_type, status, assignee_mail, created_on
    	/*
	    case status
	        when \'Resolved\' Then updated_on
	        else null
	    end as resolved_date,
	    case status
	        when \'Resolved\' Then datediff(updated_on, created_on)
	        else datediff(curdate(), created_on)
	    end as days
    	*/
	    from
	    (
	        select
	            issues.id,
	            issues.project_id,
	            projects.name as app,
	            issue_statuses.name as status,
	            author.mail as author_mail, assignee.mail as assignee_mail,
	            issues.subject, issues.created_on, issues.updated_on, issues.start_date
	            #concat(\'http:\/\/redmine.telecom.sna.samsung.com/issues/\', issues.id) as url
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

    	//Debug::dump($query_string_all);
    	return $query_string_all;
    	
    }
    
    public function get_issues_by_type_per_app_all($resultSet){
    
    	$type = array("Crash","Functional", "Usability", "By Design", "UX Flow", "ANR", "Stability", "Automation");
    
    	//$resultSet = get_all_issues_array($query_string_all);
    
    	$app = array();
    	foreach($resultSet as $row){
    		if(!array_key_exists($row['app'], $app)) {
    			$app[$row['app']] = array_fill_keys($type, 0);
    		}
    
    		$app[$row['app']][$row['issue_type']] = $app[$row['app']][$row['issue_type']] + 1;
    	}
    
    	//uasort($app, 'cmp') modified inside Device class;
    	uasort($app, array($this, 'cmp'));
    	$result = array();
    	$result[] = array_merge((array)'', $type);
    	foreach (array_keys($app) as $val) {
    		$result[] = array_merge((array)$val, array_values($app[$val]));
    	}
    	
    	// result modification for Highcharts data
    	$issue_type = array_slice($result[0], 1);
   		$result_transpose = $this->flipDiagonally($result);	 
    	$app_name = array_slice($result_transpose[0], 1);
   		$type_arr = array_slice($result_transpose,1);
		foreach($type_arr as $type){
			if (empty($type[0])) 
				$chart_data[] = ['name' => 'Undefined', 'data'=> array_slice($type, 1)];
			else	
				$chart_data[] = ['name' => $type[0], 'data'=> array_slice($type, 1)];
		}
   		$highchart_par = array('cat' => $app_name, 'dat'=> array_reverse($chart_data));
    	return $highchart_par;
    }

    public function get_issues_by_type_per_device_all($resultSet){
    	$deviceList = $this->deviceList;
    
    	$type = array("Crash","Functional", "Usability", "By Design", "UX Flow", "ANR", "Stability", "Automation");
    
    	$devices = array();
    	foreach($resultSet as $row){
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
    
    	uasort($devices, array($this, 'cmp'));
    	$result = array();
    	$result[] = array_merge((array)'', $type);
    	foreach (array_keys($devices) as $val) {
    		$result[] = array_merge((array)$val, array_values($devices[$val]));
    	}
    
    	//echo json_encode($result);
    	//return $result;

    	// result modification for Highcharts data
    	$issue_type = array_slice($result[0], 1);
    	$result_transpose = $this->flipDiagonally($result);
    	$app_name = array_slice($result_transpose[0], 1);
    	$type_arr = array_slice($result_transpose,1);
    	foreach($type_arr as $type){
    		if (empty($type[0]))
    			$chart_data[] = ['name' => 'Undefined', 'data'=> array_slice($type, 1)];
    		else
    			$chart_data[] = ['name' => $type[0], 'data'=> array_slice($type, 1)];
    	}
    	$highchart_par = array('cat' => $app_name, 'dat'=> array_reverse($chart_data));
    	return $highchart_par;
    }
    
    public function get_issues_by_device_all($resultSet){
    	$deviceList = $this->deviceList;
    
    	$devices = array();
    	foreach($resultSet as $row){
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
    
    	// result modification for Highcharts data
    	$issue_type = array_slice($result[0], 1);
    	$result_transpose = $this->flipDiagonally($result);
    	$app_name = array_slice($result_transpose[0], 1);
    	$type_arr = array_slice($result_transpose,1);
    	foreach($type_arr as $type){
    		if (empty($type[0]))
    			$chart_data[] = ['name' => 'Undefined', 'data'=> array_slice($type, 1)];
    		else
    			$chart_data[] = ['name' => $type[0], 'data'=> array_slice($type, 1)];
    	}
    	$highchart_par = array('cat' => $app_name, 'dat'=> array_reverse($chart_data));
    	return $highchart_par;
    }
    
    protected function cmp($a, $b)
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
    
    protected function flipDiagonally($arr) {
    	$out = array();
    	foreach ($arr as $key => $subarr) {
    		foreach ($subarr as $subkey => $subvalue) {
    			$out[$subkey][$key] = $subvalue;
    		}
    	}
    	return $out;
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