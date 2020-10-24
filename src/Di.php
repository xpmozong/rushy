<?php

namespace Rushy;

use Rushy\Singleton;

/**
 * 容器
 */
class Di
{
    use Singleton;

    private $register = [];

    public function set($key, $obj,...$arg):void
    {
        $this->register[$key] = array(
            "obj"=>$obj,
            "params"=>$arg,
        );
    }

    public function get($key)
    {
        if(isset($this->register[$key])){
            $obj = $this->register[$key]['obj'];
            $params = $this->register[$key]['params'];
            if(is_object($obj) || is_callable($obj)){
                return $obj;
            }else if(is_string($obj) && class_exists($obj)){
                try{
                    $this->register[$key]['obj'] = new $obj(...$params);
                    return $this->register[$key]['obj'];
                }catch (\Throwable $throwable){
                    throw $throwable;
                }
            }else{
                return $obj;
            }
        }else{
            return null;
        }
    }

    public function delete($key)
    {
        unset($this->register[$key]);
    }

    public function clear()
    {
        $this->register = array();
    }
}