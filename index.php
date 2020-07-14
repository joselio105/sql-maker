<?php

require_once 'test/Usuario.php';
require_once 'src/SqlRead.php';

use src\SqlRead;
use test\Usuario;

try {
    $sql = new SqlRead(new Usuario());
    $sql->setWhere("nome");

    echo $sql;
} catch (Exception $e) {
    echo $e->getMessage();
}
