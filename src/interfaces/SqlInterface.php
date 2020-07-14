<?php

namespace src\interfaces;

interface SqlInterface
{
    public function __construct(EntityInterface $entity);
    
    public function __toString();
}

