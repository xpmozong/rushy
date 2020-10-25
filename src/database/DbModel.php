<?php

namespace Rushy\Database;

use Rushy\Di;
use Rushy\Database\DbManager;

class DbModel
{
    protected $connectionName = 'write';
    protected $dbName = '';
    protected object $db;
    protected $tableName;

    public function __construct(array $data = [])
    {
        $this->selectDb();
    }

    protected function selectDb()
    {
        $database = Di::getInstance()->get('database');
        if ($this->dbName === '') {
            $this->dbName = $database['default'];
        }
        $this->db = DbManager::selectDb($database, $this->dbName, $this->connectionName);
    }

    public static function create(array $data = []): DbModel
    {
        return new static($data);
    }

    public function selectAll()
    {
        return $this->db->selectAll($this->tableName);
    }

    public function save()
    {

    }
}