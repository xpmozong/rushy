<?php

namespace Rushy\Database;

use Rushy\Di;
use Rushy\Database\DbFactory;

class DbManager 
{
    /**
     * @param array $config 数据库配置
     * @param string $dbname 数据库名称
     * @param string $connectionName 数据库主从
     */
    public static function addConnection($config)
    {
        try {
            Di::getInstance()->set('database', DbFactory::create($config));
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}

