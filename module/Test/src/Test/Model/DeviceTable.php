<?php
namespace Test\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Sql;
use Zend\Debug\Debug;

class DeviceTable extends AbstractTableGateway
{
    protected $table ='device';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Device());

        $this->initialize();
    }


    public function fetchAll()
    {
    	$adapter = $this->adapter;
    	$sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('custom_fields');
        $select->where('id = 9');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result= $statement->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
        	$resultSet = new ResultSet;
        	$resultSet->initialize($result);
        }	
        
        $data = $resultSet->toArray();
        foreach ($data[0] as $key => $val){
        	if ($key == 'possible_values') {
		        $devices_val = $val;
        	}
        }
        $device_string = explode("--- ", $devices_val)[1];
        // Debug::dump($device_string);
        $device_arr = explode("- ", $device_string);
        $device_arr = array_slice($device_arr, 1);
        foreach ($device_arr as $elm){
        	if(!empty($elm)){
	        	$dev_arr[] = trim($elm);
        	}
        }
        // Debug::dump($dev_arr);
        
        return array_reverse($device_arr);
    }
    
    
    public function getAlbum($id)
    {
        $id  = (int) $id;

        $rowset = $this->select(array(
            'id' => $id,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveAlbum(Album $album)
    {
        $data = array(
            'artist' => $album->artist,
            'title'  => $album->title,
        );

        $id = (int) $album->id;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getAlbum($id)) {
            $this->update(
                $data,
                array(
                    'id' => $id,
                )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteAlbum($id)
    {
        $this->delete(array(
            'id' => $id,
        ));
    }
	}