<?php

require_once 'entity/Usuario.php';
require_once 'entity/EntityFactory.php';
require_once 'src/SqlRead.php';

use src\SqlRead;
use entity\Usuario;
use entity\EntityFactory;

try {
    $sql = new SqlRead(new EntityFactory(Usuario::class));
    //$sql->setWhere("nome");

    echo $sql;
} catch (Exception $e) {
    echo $e->getMessage();
}
