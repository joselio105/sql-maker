<?php

declare(strict_types=1);

namespace src;

use src\interfaces\SqlReadInterface;
use entity\EntityInterface;

require_once 'src/interfaces/SqlReadInterface.php';
require_once 'src/SqlWhere.php';
require_once 'src/SqlJoin.php';

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
        $this->validations();
        
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
    
    private function validations()
    {
        $this->validWhere();
        $this->validOrder();
        $this->validLimit();
        $this->validJoin();
        $this->validConcat();
        $this->validSum();
    }
    
    private function validWhere()
    {
        if(!isset($this->where))
            return true;
        
        foreach ($this->where->getClauseParts() as $clauseId=>$clause)
        {
            if(empty($clause))
            {
                throw new \Exception("FALHA - setWhere(): [{$this->where->getClause($clauseId)}] String não corresponde a uma cláusula válida");
            }
            
            if($this->isValidField($clause[2]) === false)
            {
                throw new \Exception("FALHA - setWhere(): [{$clause[0]}] Cláusula não se refere a um campo da tabela");
            }
        }
    }
    
    private function validOrder()
    {
        if(!$this->isValidField($this->orderBy))
            throw new \Exception("FALHA - setOrder(): [{$this->orderBy}] Campo desconhecido para ordenar consulta");            
    }
    
    private function validLimit()
    {
        if($this->limit < 1)
            throw new \Exception("FALHA - setLimit(): [{$this->limit}] Valor da variável \$limit menor que 1");
        
        if($this->offset < 0)
            throw new \Exception("FALHA - setLimit(): [{$this->offset}] Valor da variável \$offset menor que 0");
                
    }
    
    private function validJoin()
    {   
        foreach ($this->joins as $join)
        {
            if(!in_array($join->getJoinType(), array('INNER', 'LEFT', 'RIGHT')))
                throw new \Exception("FALHA - setJoin(): [{$join->getJoinType()}] \$joinType inválido");
            
           foreach (explode('=', $join->getOnClause()) as $field)
            {
                if($this->isValidField($field) === false)
                    throw new \Exception("FALHA - setJoin(): [{$field}] Campo inválido para a cláusula \$onClause");
            }     
        }
    }
    
    private function validConcat()
    {
        foreach ($this->concats as $concat)
        {
            foreach ($concat['fields'] as $field)
            {
                if($this->isValidField($field) === false)
                    throw new \Exception("FALHA - setConcat(): [$field] Campo inválido para ser concatenado");
            }
        }
    }
    
    private function validSum()
    {
        if(isset($this->sum) AND $this->isValidField($this->sum) === false)
            throw new \Exception("FALHA - setSum(): [{$this->sum}] Campo inválido para ser feito o somatório");
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
    
    private function isValidField(string $field)
    {   
        return in_array($field, $this->fields);
    }
    
    public function showField(string $field)
    {
        if($this->isValidField($field))
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
    }

}

