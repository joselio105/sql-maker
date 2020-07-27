<?php

declare(strict_types=1);

namespace src;

use src\interfaces\SqlReadInterface;
use entity\EntityInterface;

require_once 'src/interfaces/SqlReadInterface.php';
require_once 'src/SqlWhere.php';
require_once 'src/SqlJoin.php';
require_once 'src/ReadValidations.php';

class SqlRead implements SqlReadInterface
{    
    private $entity;
    private $table;
    private $fields;
    private $fieldToShow;
    private $where;
    private $joins;
    private $orderBy;
    private $orderGrow;
    private $limit;
    private $offset;
    private $count;
    private $sum;
    private $concats;
    private $subQueries;
    private $stamments;
    
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
        $this->table = $entity->getTableName();
        $this->setFields();
        
        $this->count = false;
        
        $this->joins = [];
        $this->concats = [];
        $this->subQueries = [];
    }
    
    public function __toString()
    {
        new ReadValidations($this);
        
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
        {
            foreach ($this->concats as $alias=>$concat)
            {
                $stringFields = implode(", '{$concat['separator']}', ", $concat['fields']);
                
                $this->fields[count($this->fields)] = "CONCAT ({$stringFields}) AS {$alias}";
            }
            
            return implode(",\n\t", $this->fields)."\n";
        }
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
        
        foreach ($this->joins as $join)
            $string.= "\n{$join->__toString()}";
        
        return $string;
    }

    private function getOrder()
    {
        if(isset($this->orderBy))
            return " \nORDER BY {$this->orderBy} {$this->orderGrow}";
    }

    private function getLimit()
    {
        if(isset($this->limit))
            return " \nLIMIT {$this->offset}, {$this->limit}";
    }
    
    public function getStamments()
    {
        return $this->stamments;
    }
    
    private function setFields()
    {
        foreach ($this->entity->getAtributes() as $atribute)
        {
            $this->fields[] = "{$this->table}.{$atribute}";
        }
    }
    
    public function setFieldToShow(string $field)
    {
        $this->fieldToShow = $field;
    }

    public function setWhere(string $where)
    {
        $this->where = new SqlWhere($where, $this->fields);
        $this->stamments = $this->where->getStamments();
    }

    public function setJoin(EntityInterface $entity, string $onClause, string $joinType='INNER')
    {        
        $joinKey = count($this->joins);
        $this->joins[$joinKey] = new SqlJoin($entity, $onClause);
        $this->joins[$joinKey]->setJoinType($joinType);
        
        foreach ($this->joins[$joinKey]->getFields() as $field)
        {
            $this->fields[count($this->fields)] = $field;
        }
    }

    public function setOrder($orderBy, $descOrder=false)
    {
        $this->orderBy = (count(explode('.', $orderBy))==1 ? "{$this->table}.{$orderBy}" : $orderBy);
        
        if($descOrder)
            $this->orderGrow = " DESC";
        else 
            $this->orderGrow = " ASC";
    }

    public function setLimit(int $limit, int $offset=0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function setCount()
    {
        $this->count = true;
    }

    public function setSum(string $field)
    {
        $this->sum = (count(explode('.', $field))==1 ? "{$this->table}.{$field}" : $field);
    }

    public function setConcat(array $fields, string $alias, string $separator=" ")
    {
        foreach ($fields as $id=>$field)
            $fields[$id] = (count(explode('.', $field))==1 ? "{$this->table}.{$field}" : $field);
        
        $this->concats[$alias] = array(
            'fields' => $fields,
            'separator'=>$separator
        ); 
    }

    public function setSubQuery(SqlReadInterface $subQuery, string $alias)
    {
        $this->fields[count($this->fields)] = "({$subQuery}) AS {$alias}";
        $this->subQueries[$alias] = $subQuery;
    }
    
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function getOrderGrow()
    {
        return $this->orderGrow;
    }

    public function getLimitInt()
    {
        return $this->limit;
    }
    
    public function getOffsetInt()
    {
        return $this->offset;
    }

    public function getWhereObject()
    {
        return $this->where;
    }

    public function getFieldList()
    {
        return $this->fields;
    }

    public function getJoinObjectList()
    {
        return $this->joins;
    }
    
    public function getConcatList()
    {
        return $this->concats;
    }
    
    public function getSumField()
    {
        return $this->sum;
    }
    
    public function getSubQueryList()
    {
        return $this->subQueries;
    }
    
    public function getFieldToShow()
    {
        return $this->fieldToShow;
    }



}

