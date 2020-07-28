<?php

declare(strict_types=1);

namespace src;

require_once 'src/interfaces/SqlInterface.php';

use src\interfaces\SqlInterface;
use entity\EntityInterface;

abstract class SqlWrite implements SqlInterface
{
    
    protected $entityObject;
    protected $sqlString;
    private $stamments;
    
    public function __construct(EntityInterface $entity)
    {
        $this->entityObject = $entity;
    }

    public function __toString()
    {
        $this->validate();
        $this->setSqlString();
        
        return $this->sqlString;
    }
    
    public function getStamments()
    {
        return $this->stamments;
    }

    protected function setStamments(array $stamments)
    {
        $this->stamments = $stamments;
    }
    
    protected function getIdValue()
    {
        return $this->entityObject->getAtributeValue($this->entityObject->getPrimaryKey());
    }
    
    protected function validate()
    {}
    
    protected function validId()
    {
        if(is_null($this->getIdValue()))
            throw new \Exception("FALHA [{$this->entityObject->getPrimaryKey()}] É necessário definir um valor para a chave primaria");
    }
    
    abstract protected function setSqlString();

    
}

