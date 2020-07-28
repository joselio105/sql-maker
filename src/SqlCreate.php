<?php

declare(strict_types=1);

namespace src;

require_once 'src/SqlWrite.php';

class SqlCreate extends SqlWrite
{
    
    protected function setSqlString()
    {
        $this->sqlString = "INSERT INTO {$this->entityObject->getTableName()}({$this->getFields()}) VALUE({$this->getValues()});\nSELECT last_insert_id();";
    }
    
    private function getFields()
    {
        $fields = array_keys($this->entityObject->getAtributesValues());
        return implode(', ', $fields);
    }
    
    private function getValues()
    {
        $values = [];
        $valKey = 0;
        
        foreach ($this->entityObject->getAtributesValues() as $value)
        {
            $valueProtected = ":value{$valKey}";
            $stmts[$valueProtected] = $value;
            $values[$valKey] = (!is_null($value) ? $valueProtected : 'null');
            $valKey++;
        }
        $this->setStamments($stmts);
        
        return implode(', ', $values);
    }
    
    protected function validate()
    {
        $this->validRequiredAtributes();
    }
    
    private function validRequiredAtributes()
    {
        foreach ($this->entityObject->getAtributesValues() as $attr=>$value)
        {
            $this->checkRequiredAtribute($attr, $value);
        }
    }
    
    private function checkRequiredAtribute(string $atribute, $value)
    {
        if(is_null($value))
        {
            if(in_array($atribute, $this->entityObject->getAtributesNoRequired()) === false)
                throw new \Exception("FALHA [{$atribute}] Atributo obrigatório sem valor estabelecido");
        }
    }
}

