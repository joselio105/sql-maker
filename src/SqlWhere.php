<?php

declare(strict_types=1);

namespace src;

use entity\EntityInterface;

class SqlWhere
{
    
    private $entity;
    private $stringInitial;
    private $stringFinal;
    private $stamments;
    private $specialOperators;
    private $clauses;
    private $newClauses;
    
    public function __construct(string $where, EntityInterface $entity)
    {
        $this->entity = $entity;
        $this->stringInitial = "[{$where}]";
        $this->separateIntoClauses();
        $this->saveSpecialOperators();
        $this->setStamments();
        $this->saveStringFinal();
    }
    
    public function __toString() {
        return $this->stringFinal;
    }
    
    public function getStamments()
    {
        return $this->stamments;
    }
    
    private function separateIntoClauses()
    {
        $clausePoint = array();
        
        $mark = array(
            'start'=> array('[', ' AND ', ' OR '),
            'end'=> array(']', ' AND ', ' OR ')
        );
        
        foreach (str_split($this->stringInitial) as $position=>$character)
        {
            foreach ($mark as $label=>$markers)
            {
                foreach ($markers as $marker)
                {
                    if(substr($this->stringInitial, $position, strlen($marker)) == $marker)
                        $clausePoint[$label][] = ($label=='start' ? $position+strlen($marker) : $position);
                }
            }
        }
        
        foreach ($clausePoint['start'] as $key=>$position)
        {
            $clauseLength = $clausePoint['end'][$key] - $position;
            $this->clauses[$key] = trim(substr($this->stringInitial, $position, $clauseLength));
        }
    }
    
    private function saveSpecialOperators()
    {
        $this->specialOperators = array(
            'LIKE'=>'#'
        );
        
        foreach ($this->clauses as $key=>$clause)
        {
            foreach ($this->specialOperators as $operator=>$alias)
                $this->clauses[$key] = str_replace($operator, $alias, $clause);
        }
    }
    
    private function setStamments() {
        $pattern = '/^([\(\s]*)';                       //in√≠cio da cl·usula
        $pattern.= '([[a-z0-9\_\-\.]+)';                //campo
        $pattern.= '([\=\!\<\>\#\s]{1,4})';             //operador de comparaÁ„o
        $pattern.= '([\"\']{0,1})';                     //abre delimitador de valor
        $pattern.= '([a-zA-Z0-9\_\-\%\@\.\s]+)';        //valor
        $pattern.= '([\"\']{0,1})';                     //fecha delimitador de valor
        $pattern.= '([\)\s]*)$/';                       //fim da condiÁ„o
        
        $match = array();
        foreach ($this->clauses as $key=>$clause)
        {
            if(preg_match($pattern, $clause, $match[$key]) == 1)
            {
                if($match[$key][5] != 'IS NULL')
                {
                    $this->newClauses[$key] = str_replace($match[$key][4].$match[$key][5].$match[$key][6], ":value{$key}", $clause);
                    $this->stamments[":value{$key}"] = $match[$key][5];
                }
                
                if(!in_array($match[$key][2], $this->getAtributes($this->entity)))
                {
                    throw new \Exception("Cl·usula inv·lida - N„o corresponde a um campo da tabela");
                }
            }
            
            if(empty($match[$key]))
            {
                throw new \Exception("FALHA: [{$clause}] Clausula inv·lida");
            }
        }
    }
    
    private function saveStringFinal()
    {
        foreach ($this->newClauses as $key=>$clause)
        {
            foreach ($this->specialOperators as $operator=>$alias)
            {
                $newClause[$key] = str_replace($alias, $operator, $clause);
                $this->stringFinal = str_replace($newClause[$key], $clause, $this->stringInitial);
            }
            
            $this->stringFinal = substr($this->stringFinal, 1, strlen($this->stringFinal)-2);
        }
    }
}

