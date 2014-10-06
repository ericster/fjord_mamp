<?php
// module/Album/src/Album/Model/AlbumTable.php:
namespace Test\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Http\Header\Expect;

class IssueTable extends AbstractTableGateway
{
    protected $table ='issues';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Issue());

        $this->initialize();
    }
//     public function fetchAll_4()
    public function fetchAll()
    {
    	$adapter = $this->adapter;
    	$sql = new Sql($adapter);
    	$subQuery = $sql->select();
    	$subQuery->from('issues');
//     	$subQuery->columns(array('id', 'subject', 'created' => 'created_on', 'count' => new Expression('Count(*)')));
    	$subQuery->columns(array('id', 'subject', 'created' => 'created_on'));
    	$subQuery->join('issue_statuses', 'issues.status_id = issue_statuses.id', array('status' => 'name'));
    	$subQuery->join('projects', 'issues.project_id = projects.id', array('app' => 'name'));
    	$notdailytracking = array(1,2,3,4,7,8,9,11,15,17,18);
    	 
    	// VUX projects = '95'
    	$subVux = $sql->select();
    	$subVux->from(array('p' => 'projects'));
    	$subVux->join(array('p1' => 'projects'), 'p1.parent_id = p.id', array('p1id' => 'id'), 'left' );
    	$subVux->join(array('p2' => 'projects'), 'p2.parent_id = p1.id', array('p2id' => 'id'), 'left');
    	$subVux->where('p.id = 95');
    	$subQuery->join(array('vux' => $subVux), 'project_id = vux.p2id' , array('p2id'));
    
    	// Devices = '9'
    	$subDevice = $sql->select();
    	$subDevice->from('issues');
    	$subDevice->columns(array('id'));
    	$subDevice->join('custom_values', 'customized_id = issues.id', array('devices' => 'value'));

    	$deviceList = 'custom_values.value like "N910A%" OR custom_values.value like "NDA%"';
    	$subDevice->where(array('custom_values.custom_field_id = :custom_no', $deviceList));

    	$subQuery->join(array('sub_dev' => $subDevice), 'issues.id = sub_dev.id', array('devices') );

    	// Issue Type = '32'
    	$subType = $sql->select();
    	$subType->from('issues');
    	$subType->columns(array('id'));
    	$subType->join('custom_values', 'customized_id = issues.id', array('type' => 'value'));
    	$subType->where(array('custom_values.custom_field_id' => '32'));

    	$subQuery->join(array('sub_type' => $subType), 'issues.id = sub_type.id', array('type') );
    
//     	$subQuery->where->in('issues.status_id', $notdailytracking);
    	$subQuery->order('issues.id desc');
    	
//     	echo($subQuery->getSqlString());
    	 
    	// Named parameter: '9' => 'devices', '32' => 'issue_type', '6' => 'plm'
    	$statement = $sql->prepareStatementForSqlObject($subQuery);
    	$result= $statement->execute(array(':custom_no'=> '9'));
    
    	if ($result instanceof ResultInterface && $result->isQueryResult()) {
    		$resultSet = new ResultSet;
    		$resultSet->initialize($result);
    	}
    	 
    	return $resultSet;
    }

    public function fetchAll_3()
    {
    	$adapter = $this->adapter;
    	$sql = new Sql($adapter);
    	$subQuery = $sql->select();
    	$subQuery->from('issues');
    	$subQuery->columns(array('id', 'subject', 'created' => 'created_on'));
    	$subQuery->join('issue_statuses', 'issues.status_id = issue_statuses.id', array('status' => 'name'));
    	$subQuery->join('projects', 'issues.project_id = projects.id', array('app' => 'name'));
    	$notdailytracking = array(1,2,3,4,7,8,9,11,15,17,18);
    	 
    	// VUX projects = '95'
    	$subVux = $sql->select();
    	$subVux->from(array('p' => 'projects'));
    	$subVux->join(array('p1' => 'projects'), 'p1.parent_id = p.id', array('p1id' => 'id'), 'left' );
    	$subVux->join(array('p2' => 'projects'), 'p2.parent_id = p1.id', array('p2id' => 'id'), 'left');
    	$subVux->where('p.id = 95');
    	$subQuery->join(array('vux' => $subVux), 'project_id = vux.p2id' , array('p2id'));
    
    	// Group By device
    	$subDevice = $sql->select();
    	$subDevice->from('issues');
    	$subDevice->columns(array('id'));
    	$subDevice->join('custom_values', 'customized_id = issues.id', array('devices' => new Expression("Group_Concat(value separator '; ')")));
    	$subDevice->group(array('custom_values.customized_id', 'custom_values.custom_field_id'));
    	$subDevice->where('custom_values.custom_field_id = :custom_no');
    	 
    
    	$subQuery->join(array('sub_dev' => $subDevice), 'issues.id = sub_dev.id', array('devices'), 'left');
    
    	$subQuery->where->in('issues.status_id', $notdailytracking);
    	$subQuery->order('issues.id desc');
    	
//     	echo($subQuery->getSqlString());
    	 
    	// Named parameter: '9' => 'devices', '32' => 'issue_type', '6' => 'plm'
    	$statement = $sql->prepareStatementForSqlObject($subQuery);
    	$result= $statement->execute(array(':custom_no'=> '9'));
    
    	if ($result instanceof ResultInterface && $result->isQueryResult()) {
    		$resultSet = new ResultSet;
    		$resultSet->initialize($result);
    	}
    	 
    	return $resultSet;
    }

    public function fetchAll_2()
//     public function fetchAll()
    {
    	// Raw query pass
    	$sql = 'select issues.id, issues.subject, issues.project_id, projects.name as app, devices,  issue_statuses.name as status
    from issues
    join issue_statuses on issues.status_id = issue_statuses.id 
    join projects on issues.project_id = projects.id 

    left join
    (   
        select issues.id, group_concat(value separator \'; \') as devices from issues, custom_values
        where
            issues.id = custom_values.customized_id and 
            custom_values.custom_field_id = \'9\' 
        group by custom_values.custom_field_id, custom_values.customized_id
    ) devices
    on issues.id = devices.id

    join
    (   
        select p2.id
        from projects p
            left join projects as p1 on p1.parent_id = p.id
            left join projects as p2 on p2.parent_id = p1.id
        where
            p.id = 95
    ) vux_projects
    on project_id = vux_projects.id

    where issues.status_id not in (5, 6, 10, 12, 13, 14, 16, 19) 
    order by issues.id desc'; 
    	// Raw query 
    	$sql = 'select issues.id, issues.subject, devices, issues.project_id, projects.name as project, issue_statuses.name as status
    	from issues
    	join issue_statuses on issues.status_id = issue_statuses.id
    	join projects on issues.project_id = projects.id
    	
    	join
    	(
    	select p2.id
    	from projects p
    	left join projects as p1 on p1.parent_id = p.id
    	left join projects as p2 on p2.parent_id = p1.id
    	where
    	p.id = 95
    	) vux_projects
    	on project_id = vux_projects.id
    	
    	join
    	(
    	select issues.id, value as devices
    	from issues
    	join custom_values on issues.id = custom_values.customized_id
    	where custom_values.custom_field_id = \'9\' and custom_values.value = \'N910A T ATT\'")
    	) devices
    	on issues.id = devices.id
    	
    	join
    	(
    	select issues.id, value as type
    	from issues
    	join custom_values on issues.id = custom_values.customized_id
    	where custom_values.custom_field_id = \'32\'
    			) issue_type
    			on issues.id = issue_type.id
    	
    	order by issues.id desc';
    			
    	$sql = 'select * from issues';
    	$resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    	return $resultSet;
    }

    public function fetchAll_1()
    {
    	$adapter = $this->adapter;
    	$sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('issues');
        $select->columns(array('id', 'subject', 'created' => 'created_on'));
        $select->join('issue_statuses', 'issues.status_id = issue_statuses.id', array('status' => 'name'));
        $select->join('projects', 'issues.project_id = projects.id', array('app' => 'name'));
        $notdailytracking = array(1,2,3,4,7,8,9,11,15,17,18);
        $select->where->in('issues.status_id', $notdailytracking);
        $select->order('issues.id desc');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result= $statement->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
        	$resultSet = new ResultSet;
        	$resultSet->initialize($result);
        }	
        return $resultSet;
    }

	}