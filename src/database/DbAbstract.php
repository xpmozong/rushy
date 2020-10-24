<?php

namespace Rushy\Database;

abstract class DbAbstract
{
    abstract function selectAll($table);

    abstract function save();
}