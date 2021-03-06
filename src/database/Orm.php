<?php

namespace Rushy\Database;

use Rushy\Database\DbAbstract;

class Orm extends DbAbstract
{
    protected \PDO $pdo;
    public function __construct($config)
    {
        $this->pdo = new \PDO(
            'mysql:host='.$config['host'].';dbname='.$config['dbname'], 
            $config['username'], 
            $config['password'],
            $config['options']
        );
    }

    public function selectAll($table)
    {
        $statement = $this->pdo->prepare("select * from {$table}");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS);
    }

    public function save()
    {

    }

    public function insert($table, $parmeters)
    {
        
        $sql = sprintf(
                "insert into %s (%s) values (%s)", 
                $table, 
                implode(',', array_keys($parmeters)), 
                ':'.implode(',:', array_keys($parmeters))
        );
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parmeters);
        } catch (\Throwable $th) {
            // die($th->getMessage());
            throw new \Exception('数据库出错啦！');
        }
    }
}