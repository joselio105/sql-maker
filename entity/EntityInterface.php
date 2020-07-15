<?php

namespace entity;

interface EntityInterface
{
    public function __construct($entity);
    
    public function getAtributes();
    
    public function getAtributesValues();
    
    public function getAtributeValue(string $atributeName);
    
    public function setAtributesValues(array $values);
    
    public function setAtributeValue(string $atributeName, $value);
    
    public function getEntityClassName();
    
    public function getTableName();
}
