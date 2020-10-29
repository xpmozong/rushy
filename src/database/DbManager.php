<?php

namespace Rushy\Database;

use Rushy\Di;

class DbManager 
{
    /**
     * @param array $config 数据库配置
     */
    public static function addConnection($config)
    {
        try {
            $db = [];
            $db['default'] = $config['default']['dbname'];
            foreach ($config as $value) {
                $driver = isset($value['driver']) ? $value['driver'] : 'rmysql';
                $class = 'Rushy\\Database\\'.ucfirst($driver);
                if (isset($value['master'])) {
                    $value['master']['dbname'] = $value['dbname'];
                    $db[$value['dbname']]['dbname'] = $value['dbname'];
                    $db[$value['dbname']]['driver'] = $value['driver'];
                    $db[$value['dbname']]['master'] = new $class($value['master']);
                    if (isset($value['slave'])) {
                        $db[$value['dbname']]['slave_count'] = count($value['slave']);
                        foreach ($value['slave'] as $k => $config) {
                            $config['dbname'] = $value['dbname'];
                            $db[$value['dbname']]['slave'][$k] = new $class($config);
                        }
                    }
                } else {
                    throw new \Exception('数据库配置文件要有master');
                }
            }
            Di::getInstance()->set('database', $db);
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    /**
     * 选择数据库
     * @param array $database 数据库连接数组
     * @param string $dbName 数据库名称
     * @param string $connectionName 连接类型
     */
    public static function selectDb($database, $dbName, $connectionName)
    {
        if ($connectionName === 'write') {
            return $database[$dbName]['master'];
        } else {
            if (isset($database[$dbName]['slave'])) {
                $slave_count = $database[$dbName]['slave_count'];
                $slave_rand = mt_rand(0, $slave_count - 1);
                return $database[$dbName]['slave'][$slave_rand];
            }
        }

        return null;
    }
}

