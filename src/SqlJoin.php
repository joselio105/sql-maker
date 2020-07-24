<?php

declare(strict_types=1);

namespace src;

use entity\EntityInterface;

class SqlJoin
{
    private $joinType;
    private $tableName;
    private $onClause;
    private $fields;
    
    public function __construct(EntityInterface $entity, string $onClause)
    {
        $this->joinType = 'INNER';
        $this->tableName = $entity->getTableName(); 
        $this->onClause = $onClause;
        $this->fields = [];
        
        foreach ($entity->getAtributes() as $field)
        {
            $this->fields[count($this->fields)] = "{$this->tableName}.{$field}";
        }
    }
    
    public function __toString()
    {
        $string = "{$this->joinType} JOIN {$this->tableName} ON {$this->onClause}";
        
        return $string;
    }
    
    public function getJoinType()
    {
        return $this->joinType;
    }

    public function getOnClause()
    {
        return $this->onClause;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setJoinType(string $joinType)
    {
        $this->joinType = $joinType;
    }

}

