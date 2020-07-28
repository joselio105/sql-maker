<?php

declare(strict_types=1);

namespace src;

require_once 'src/SqlWrite.php';

class SqlUpdate extends SqlWrite
{
    protected function setSqlString()
    {
        $this->sqlString = "UPDATE {$this->entityObject->getTableName()} SET {$this->getFieldValueString()} WHERE id={$this->getIdValue()}";
    }
    
    private function getFieldValues()
    {
        $value = [];
        $valKey = 0;
        
        foreach ($this->entityObject->getAtributesValues() as $attr=>$val)
        {
            if(!is_null($val) AND $attr!=$this->entityObject->getPrimaryKey())
            {
                $valueProtected = ":value{$valKey}";
                $stmts[$valueProtected] = $value;
                $value[$attr] = $valueProtected;
                $valKey++;
            }
        }
        $this->setStamments($stmts);
        
        return $value;
    }
    
    private function getFieldValueString()
    {
        $response = "";
        
        foreach ($this->getFieldValues() as $attr=>$val)
        {
            $response.= "{$attr} = {$val}, ";
        }
        
        return substr($response, 0, -2);
    }
    
    protected function validate()
    {
        $this->validId();
        $this->validAtributeValues();
    }
    
    private function validAtributeValues()
    {
        if(empty($this->getFieldValues()))
            throw new \Exception("FALHA [{$this->entityObject->getTableName()}] É necessário definir o valor de ao menos um atributo da classe");
    }
}