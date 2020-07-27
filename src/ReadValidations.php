<?php

declare(strict_types=1);

namespace src;

use src\interfaces\SqlReadInterface;

class ReadValidations
{
    private $query;
    
    public function __construct(SqlReadInterface $query)
    {
        $this->query = $query;
        $this->validWhere();
        $this->validOrder();
        $this->validLimit();
        $this->validJoinList();
        $this->validConcatList();
        $this->validSubQueries();
        $this->validSum();
        $this->validFieldToShow();
    }
    
    private function validWhere()
    {
        if(is_null($this->query->getWhereObject()))
            return true;
            
        foreach ($this->query->getWhereObject()->getClauseParts() as $clauseId=>$clause)
        {
            $this->checkWhereClauseString($clauseId, $clause);
            $this->checkWhereClauseField($clause);
        }
    }
    
    private function checkWhereClauseString(int $clauseId, array $clause)
    {
        if(empty($clause))
            throw new \Exception("FALHA - setWhere(): [{$this->where->getClause($clauseId)}] String não corresponde a uma cláusula válida");
    }
    
    private function checkWhereClauseField(array $clause)
    {
        try {
            $clauseIndex = 2;
            $this->checkField($clause[$clauseIndex]);
        }catch (\Exception $e)
        {
            throw new \Exception("FALHA - setWhere(): {$e->getMessage()}");
        }
    }
    
    private function validOrder()
    {
        if(is_null($this->query->getOrderBy()))
            return true;
            
        try {
            $this->checkField($this->query->getOrderBy());
        } catch (\Exception $e) {
            throw new \Exception("FALHA - setOrder(): {$e->getMessage()}");
        }            
    }
    
    private function validLimit()
    {
        if(is_null($this->query->getLimitInt()))
            return true;
        
        if($this->query->getLimitInt() < 1)
            throw new \Exception("FALHA - setLimit(): [{$this->query->getLimitInt()}] Valor da variável \$limit menor que 1");
            
        if($this->query->getOffsetInt() < 0)
            throw new \Exception("FALHA - setLimit(): [{$this->query->getOffsetInt()}] Valor da variável \$offset menor que 0");
    }
    
    private function validJoinList()
    {
        foreach ($this->query->getJoinObjectList() as $join)
        {
            $this->checkJoinType($join->getJoinType());
            $this->checkJoinOnClause($join->getOnClause());
        }
    }
    
    private function checkJoinType(string $joinType)
    {
        if(in_array($joinType, array('INNER', 'LEFT', 'RIGHT')) === false)
            throw new \Exception("FALHA - setJoin(): [{$joinType}] \$joinType inválido");
    }
    
    private function checkJoinOnClause(string $onClause)
    {
        foreach (explode('=', $onClause) as $field)
        {
            try {
                $this->checkField($field);
            } catch (\Exception $e) {
                throw new \Exception("FALHA - setJoin(): {$e->getMessage()}");
            }
        } 
    }
    
    private function validConcatList()
    {
        foreach ($this->query->getConcatList() as $concat)
        {
            $this->checkConcatFieldList($concat['fields']);
        }
    }
    
    private function checkConcatFieldList(array $fieldList)
    {
        foreach ($fieldList as $field)
        {
            try {
                $this->checkField($field);
            } catch (\Exception $e) {
                throw new \Exception("FALHA - setConcat(): {$e->getMessage()}");
            }
        }
    }
    
    private function validSum()
    {
        if(is_null($this->query->getSumField()))
            return true;
        
        try {
            $this->checkField($this->query->getSumField());
        } catch (\Exception $e) {
            throw new \Exception("FALHA - setSum(): {$e->getMessage()}");
        }
    }
    
    private function validSubQueries()
    {
        foreach ($this->query->getSubQueryList() as $subQuery)
        {
            $this->checkSubQuery($subQuery);
        }
    }
    
    private function checkSubQuery(SqlReadInterface $subQuery)
    {
        if(is_null($subQuery->getFieldToShow()))
        {
            throw new \Exception("FALHA - setSubQuery(): É necessário definir um único campo para a subquery");
        }
        
        try {
            $this->checkField($subQuery->getFieldToShow());
        } catch (\Exception $e) {
            throw new \Exception("FALHA - setSubQuery(): {$e->getMessage()}");
        }
    }
    
    private function validFieldToShow()
    {
        if(is_null($this->query->getFieldToShow()))
            return true;
        
        try {
            $this->checkField($this->query->getFieldToShow());
        } catch (\Exception $e) {
            throw new \Exception("FALHA - setFieldToShow(): {$e->getMessage()}");
        }
    }
    
    private function checkField(string $field)
    {
        if(in_array($field, $this->query->getFieldList()) === false)
            throw new \Exception("[{$field}] Campo inválido");
    }
}

