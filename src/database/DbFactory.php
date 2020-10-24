<?php

namespace Rushy\Database;

class DbFactory
{
    public static function create($database)
    {
        $db = [];
        $db['default'] = $database['default']['dbname'];
        foreach ($database as $key => $value) {
            $driver = isset($value['driver']) ? $value['driver'] : 'rmysql';
            if (isset($value['master'])) {
                $value['master']['dbname'] = $value['dbname'];
                $db[$value['dbname']]['dbname'] = $value['dbname'];
                $db[$value['dbname']]['driver'] = $value['driver'];
                $db[$value['dbname']]['master'] = self::$driver($value['master']);
                if (isset($value['slave'])) {
                    $db[$value['dbname']]['slave_count'] = count($value['slave']);
                    foreach ($value['slave'] as $k => $config) {
                        $config['dbname'] = $value['dbname'];
                        $db[$value['dbname']]['slave'][$k] = self::$driver($config);
                    }
                }
            } else {
                throw new \Exception('数据库配置文件要有master');
            }
        }
        return $db;
    }

    public static function selectDb($database, $dbName, $connectionName)
    {
        $class = 'Rushy\\Database\\'.ucfirst($database[$dbName]['driver']);
        if ($connectionName === 'write') {
            return new $class($database[$dbName]['master']);
        } else {
            if (isset($database[$dbName]['slave'])) {
                $slave_count = $database[$dbName]['slave_count'];
                $slave_rand = mt_rand(0, $slave_count - 1);
                return new $class($database[$dbName]['slave'][$slave_rand]);
            }
        }
    }

    public static function orm($config)
    {
        return new \PDO(
            'mysql:host='.$config['host'].';dbname='.$config['dbname'], 
            $config['username'], 
            $config['password'],
            $config['options']
        );
    }
}