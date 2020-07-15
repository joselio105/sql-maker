<?php

declare(strict_types=1);

namespace entity;

require_once 'entity/EntityInterface.php';

class EntityFactory implements EntityInterface
{
    private $entityClassName;
    private $enityObject;
    private $entityNamespace;
    private $reflectionClass;
    private $atributes;
    private $methods;

    public function __construct($entity)
    {
        $this->enityObject = new $entity;
        $this->reflectionClass = new \ReflectionClass($this->enityObject);
        $this->entityClassName = $this->reflectionClass->getShortName();
        $this->entityNamespace = $this->reflectionClass->getNamespaceName();
        $this->setAtributes();
        $this->setMethods();
        $this->validate();
    }
    
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }
    
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }
    
    public function getAtributes()
    {
        return $this->atributes;
    }

    public function getAtributesValues()
    {
        $values = array();
        
        foreach ($this->atributes as $atributeName)
        {
            $this->getAtributeValue($atributeName);
        }
        
        return $values;
    }

    public function getAtributeValue(string $atributeName)
    {
        $function = "get".ucfirst($atributeName);
        return $function();
    }

    public function getTableName()
    {
        $className = str_replace('T', '_t', $this->entityClassName);        
        return strtolower($className);
    }

    public function setAtributesValues(array $values)
    {
        foreach ($values as $key=>$value)
        {
            $atributeName = $this->atributes[$key];
            $this->setAtributeValue($atributeName, $value);
        }
    }
    
    public function setAtributeValue(string $atributeName, $value)
    {
        $function = 'set'.ucfirst($atributeName);
        $this->enityObject->$function($value);
    }

    private function setAtributes()
    {
        foreach ($this->reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE) as $atribute)
            $this->atributes[] = $atribute->name;
    }
    
    private function setMethods()
    {
        foreach ($this->reflectionClass->getMethods(\ReflectionProperty::IS_PUBLIC) as $method)
            $this->methods[] = $method->name;
    }
    
    private function validate() 
    {
        if(empty($this->atributes))
            throw new \Exception("FALHA: [{$this->entityClassName}] Classe sem atributos");
        
        foreach ($this->atributes as $atribute)
        {
            foreach (array('get', 'set') as $functioPrefix)
            {
                $atribute = ucfirst($atribute);
                $functionName = "{$functioPrefix}{$atribute}";
                if(!in_array($functionName, $this->methods))
                    throw new \Exception("FALHA: [{$this->entityClassName}] Classe sem o método {$functionName}");
            }
        }
    }

}

