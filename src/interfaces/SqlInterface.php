<?php

namespace src\interfaces;

use entity\EntityInterface;

interface SqlInterface
{
    public function __construct(EntityInterface $entity);
    
    public function __toString();
}

