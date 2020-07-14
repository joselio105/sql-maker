<?php

namespace src\traits;

use src\interfaces\EntityInterface;

trait EntityHandlerTrait
{
    private $recursive;    
    
    /**
     * Lista um array com as propriedades de uma classe Entity
     * @param EntityInterface $entity
     * @return string[]
     */
    public function getProperties(EntityInterface $entity)
    {
        $classAttrs = $this->getClassAttrs($entity);
        
        return $classAttrs['properties'];
    }
    
    /**
     * Seta o valor de uma determinada propriedade
     * @param EntityInterface $entity
     * @param string $property
     * @param mixed $value
     */
    public function setProperty(EntityInterface $entity, string $property, $value)
    {
        $classAttrs = $this->getClassAttrs($entity);
        $setFunction = "set".ucfirst($property);
        
        if(in_array($setFunction, $classAttrs['methods']))
            $entity->$setFunction($value);
    }
    
    /**
     * Seta o valor de todas as propriedades de uma entity
     * @param EntityInterface $entity
     * @param object $values
     */
    public function setProperties(EntityInterface $entity, array $values)
    {
        $id = 0;
        foreach($this->listProperties($entity) as $ent=>$properties)
        {
            foreach ($properties as $property)
            {
                $this->setProperty($ent, $property, $values[$id]);
                $id++;
            }
        }
    }
    
    private function listProperties(EntityInterface $entity)
    {
        $list = array();
        
        $classAttr = $this->getClassAttrs($entity);
        if($classAttr['parentClass'])
        {
            $entity = 
            $list[$classAttr['parentClass']['className']] = $classAttr['parentClass']['properties'];
        }
        
        foreach ($this->getProperties($entity) as $property)
        {
            $getFunction = "get".ucfirst($property);
            $subEntity = $entity->$getFunction();
            if(is_object($subEntity))
            {
                $list[get_class($subEntity)] = $this->getProperties($subEntity);
            }else 
            {
                $list[get_class($entity)] = $this->getProperties($entity);
            }
        }
        
        return $list;
    }
    
    /**
     * Recupera o valor de uma determinada propriedade da classe 
     * @param EntityInterface $entity
     * @param string $property
     * @return mixed
     */
    public function getProperty(EntityInterface $entity, string $property)
    {
        $function = "get".ucfirst($property);
        return $entity->$function();
    }
    
    /**
     * Recupera o nome da tabela do banco de dados relativa à classe
     * @param EntityInterface $entity
     * @return string
     */
    public function getTableName(EntityInterface $entity)
    {
        $classAttrs = $this->getClassAttrs($entity);
        if($classAttrs['parentClass'])
            $className = $classAttrs['parentClass']['className'];
        else 
            $className = $classAttrs['className'];
        
        $className = str_replace('T', '_t', $className);
        
        return strtolower($className);
    }
    
    public function getClassAttrs(EntityInterface $entity)
    {
        $api = new \ReflectionClass($entity);
        $this->recursive = true;
        
        return $this->getAttrs($api, $entity);
        
    }
    
    private function getAttrs(\ReflectionClass $api, EntityInterface $entity)
    {
        $classAttrs = array(
            'object'=>$entity,
            'namespace'=>$api->getNamespaceName(),
            'className'=>substr($api->getName(), strlen($api->getNamespaceName())+1),
            'properties'=>$this->getClassProperties($api),
            'methods'=>$this->getClassMethods($api)
        );
        
        if($this->recursive)
        {
            $classAttrs['parentClass'] = $this->getParentClassAttrs($api);
        }
        
        return $classAttrs;
    }
    
    private function getClassProperties(\ReflectionClass $api)
    {
        $list = array();
        
        foreach ($api->getProperties(\ReflectionProperty::IS_PRIVATE) as $item)
            $list[] = $item->name;
        
        return $list;
    }
    
    private function getClassMethods(\ReflectionClass $api)
    {
        $list = array();
        
        foreach ($api->getMethods(\ReflectionProperty::IS_PUBLIC) as $item)
        {
            if($item->class == $api->getName())
                $list[] = $item->name;
        }
            
        return $list;
    }
    
    private function getParentClassAttrs(\ReflectionClass $api)
    {
        if($api->getParentClass())
        {
            $this->recursive = false;
            $parentApi = new \ReflectionClass($api->getParentClass()->name);
            return $this->getAttrs($parentApi);
        }else 
            return false;
    }
}

