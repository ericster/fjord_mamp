<?php
// module/Album/src/Album/Model/AlbumTable.php:
namespace Album\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\TableGateway\AbstractTableGateway;

class AlbumTable extends AbstractTableGateway
{
    protected $table ='album';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Album());

        $this->initialize();
    }

    public function fetchAll()
    {
//        $resultSet = $this->select();
//        return $resultSet;
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('album');
        $select->where(array('id' => 5));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        echo $sql->getSqlStringForSqlObject($select);
        
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
        	$resultSet = new ResultSet;
        	$resultSet->initialize($result);
        
//         	foreach ($resultSet as $row) {
//         		echo $row->my_column . PHP_EOL;
//         	}
        }
        
        return $resultSet;
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