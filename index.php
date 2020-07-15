<?php

require_once 'entity/Usuario.php';
require_once 'entity/Cargo.php';
require_once 'entity/EntityFactory.php';
require_once 'src/SqlRead.php';
require_once 'src/ReadFactory.php';

use src\SqlRead;
use entity\Usuario;
use entity\Cargo;
use entity\EntityFactory;
use src\ReadFactory;

$result = new ReadFactory();

if(is_string($result()))
    echo $result();
/*
try {
    $sql = new SqlRead(new EntityFactory(Usuario::class));
    $sql->setOrder("nome");
    $sql->setWhere('usuario.nome=bbb');
    $sql->setJoin(new EntityFactory(Cargo::class), "usuario.cargo=cargo.id");

    echo $sql;
} catch (Exception $e) {
    echo $e->getMessage();
}
*/
