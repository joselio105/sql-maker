<?php

declare(strict_types=1);

namespace src;

use entity\EntityFactory;
use entity\Usuario;
use entity\Cargo;
use entity\InvalidEntity;

class ReadFactory
{
    public function __invoke()
    {
        try {
            //Construct
            //01
            $sql = new SqlRead(new EntityFactory(new Usuario()));
            //02
            //$sql = new SqlRead(new EntityFactory(new InvalidEntity()));
            
            //setWhere
            //03
            //$sql->setWhere("usuario.nome LIKE 'a%'");
            //04
            //$sql->setWhere("usuario.nome LIKE 'a%' AND usuario.nascimento < '1990-01-01'");
            //05
            //$sql->setWhere("asdf AND nome LIKE 'a%'");
            //06
            //$sql->setWhere("usuario.nome LIKE 'a%' AND usuario.data < '1990-01-01'");
            
            //setOrder
            //07
            //$sql->setOrder("nome", true);
            //08
            $sql->setOrder("usuario.nascimento", false);
            //09
            //$sql->setOrder("nota", true);
            
            //setLimit
            //10
            $sql->setLimit(5, 0);
            //11
            //$sql->setLimit(0, 1);            
            //12
            //$sql->setLimit(10, -1);
            
            //setJoin
            //28
            //$sql->setWhere("usuario.nome LIKE 'a%' AND cargo.nome = 'Gerente'");
            //13
            $sql->setJoin(new EntityFactory(new Cargo()), "usuario.cargo=cargo.id", "INNER");
            //14
            //$sql->setJoin(new EntityFactory(InvalidEntity::class), "usuario.cargo=cargo.id", "LEFT");
            //15
            //$sql->setJoin(new EntityFactory(Cargo::class), "usuario.cargo=id", "RIGHT");
            //16
            //$sql->setJoin(new EntityFactory(Cargo::class), "usuario.cargo=cargo.id", "a");
            
            //Extras
            //29
            $sql->setOrder("cargo.nome", false);
            
            //setConcat
            //30
            //$sql->setConcat(array('sobrenome', 'nome'), "nomeCompleto", ", ");
            //31
            $sql->setConcat(array('cargo.nome', 'nome'), "server", " - ");
            //32
            //$sql->setConcat(array('cargo.nome', 'idade'), "bla", " - ");
            
            //setSubQuery
            //32
            $subQuery = new SqlRead(new EntityFactory(new Usuario()));
            $subQuery->setCount();
            $subQuery->setFieldToShow("usuario.nome");
            $sql->setSubQuery($subQuery, 'teste');
            
            $result = $sql->__toString();
        } catch (\Exception $e) {
            $result = $e;
        }
        
        return $result;
    }
}

