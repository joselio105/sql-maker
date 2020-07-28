<?php
header("Content-type: text/html; charset=utf-8");

require_once 'src/CreateFactory.php';

use src\CreateFactory;

$result = new CreateFactory();

if(is_string($result()))
    echo $result();
else 
    echo "Exceção: {$result()->getMessage()}";
