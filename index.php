<?php
header("Content-type: text/html; charset=utf-8");

require_once 'entity/Usuario.php';
require_once 'entity/Cargo.php';
require_once 'entity/InvalidEntity.php';
require_once 'entity/EntityFactory.php';
require_once 'src/SqlRead.php';
require_once 'src/ReadFactory.php';

use src\ReadFactory;

$result = new ReadFactory();

if(is_string($result()))
    echo $result();
else 
    echo "Exceção: {$result()->getMessage()}";
