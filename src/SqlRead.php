<?php

declare(strict_types=1);

namespace src;

use src\interfaces\SqlReadInterface;
use entity\EntityInterface;

require_once 'src/interfaces/SqlReadInterface.php';
require_once 'src/SqlWhere.php';

class SqlRead implements SqlReadInterface
{    
    private $entity;
    private $table;
    private $fields;
    private $fieldToShow;
    private $where;
    private $joins;
    private $order;
    private $limit;
    private $count;
    private $sum;
    private $concats;
    private $stamments;
    
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
        $this->table = $entity->getTableName();
        $this->fields = $entity->getAtributes();
        
        $this->count = false;
        
        $this->joins = array();
        $this->concats = array();
        $this->subQueries = array();
    }
    
    public function __toString()
    {
        $sql = "SELECT {$this->getFields()}FROM {$this->table}{$this->getWhere()}{$this->getJoins()}{$this->getOrder()}{$this->getLimit()}";
        
        return $sql;
    }
    
    private function getFields()
    {
        if($this->count)
            return "COUNT(*) AS total ";
        
        if(isset($this->sum))
            return "SUM({$this->sum}) AS soma ";

        if(isset($this->fieldToShow))
            return "{$this->fieldToShow} ";
        else
            return implode(",\n\t", $this->fields)."\n";
    }
    
    private function getWhere()
    {
        if(isset($this->where))
            return "\nWHERE {$this->where}";
    }

    private function getJoins()
    {
        $string = "";
        
        if(!empty($this->joins))
            $string = "\n".implode("\n", $this->joins);
        
        return $string;
    }

    private function getOrder()
    {
        if(isset($this->order))
            return " \nORDER BY {$this->order}";
    }

    private function getLimit()
    {
        if(isset($this->limit))
            return " \nLIMIT {$this->limit}";
    }
    
    public function getStamments()
    {
        return $this->stamments;
    }
    
    public function showField(string $field)
    {
        if(in_array($field, $this->fields))
            $this->fieldToShow = $field;
    }

    public function setWhere(string $where)
    {
        $this->where = new SqlWhere($where, $this->entity);
        $this->stamments = $this->where->getStamments();
    }

    public function setJoin(EntityInterface $entity, string $on, $joinType='INNER')
    {
        $tableName = $entity->getTableName();
        
        foreach ($entity->getAtributes() as $field)
        {
            $this->fields[count($this->fields)] = "{$tableName}.{$field}";
        }
        
        $tableName = (is_null($tableAlias) ? $this->getTableName($entity) : "{$this->getTableName($entity)} AS {$tableAlias}");
        $this->joins[count($this->joins)] = "{$joinType} JOIN {$tableName} ON {$on}";
    }

    public function setOrder($orderBy, $descOrder=false)
    {
        var_dump("Verificar também caso não se tenha passado a tabela do campo");die;
        if(!in_array($orderBy, $this->fields))
            throw new \Exception("FALHA: [{$orderBy}] Campo desconhecido para ordenar consulta");
        
        $this->order = $orderBy;
        if($descOrder)
            $this->order.= " DESC";
        else 
            $this->order.= " ASC";
    }

    public function setLimit(int $limit, int $offset=0)
    {
        if($limit < 1)
            throw new \Exception("FALHA: [{$limit}] Valor da variável \$limit menor que 1");
        if($offset < 0)
            throw new \Exception("FALHA: [{$offset}] Valor da variável \$offset menor que 0");
        
        $this->limit = "{$offset}, {$limit}";
    }

    public function setCount()
    {
        $this->count = true;
    }

    public function setSum(string $field)
    {
        if(in_array($field, $this->fields))
            $this->sum = $field;
    }

    public function setConcat(array $fields, string $alias, string $separator="' '")
    {
        foreach ($fields as $field)
        {
            if(!array_search($field, $this->fields))
                return;
        }
        $fields = implode(", {$separator}, ", $fields);
        $this->fields[count($this->fields)] = "CONCAT ({$fields}) AS {$alias}";
    }

    public function setSubQuery($subQuery, string $alias)
    {
        $this->fields[count($this->fields)] = "({$subQuery}) AS {$alias}";
    }

}

