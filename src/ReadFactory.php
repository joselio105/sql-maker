<?php

declare(strict_types=1);

namespace src;

use entity\EntityFactory;
use entity\Usuario;

class ReadFactory
{
    public function __invoke()
    {
        try {
            $sql = new SqlRead(new EntityFactory(Usuario::class));
            $sql->setLimit(10);
            
            $result = $sql->__toString();
        } catch (\Exception $e) {
            $result = $e;
        }
        
        return $result;
    }
}

