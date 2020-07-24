<?php

declare(strict_types=1);

namespace src;

class SqlWhere
{
    
    private $fields;
    private $stringInitial;
    private $stringFinal;
    private $stamments;
    private $specialOperators;
    private $clauses;
    private $clauseParts;
    private $newClauses;
    
    public function __construct(string $where, array $fields)
    {
        $this->fields = $fields;
        $this->stringInitial = "[{$where}]";
        $this->clauses = [];
        $this->newClauses = [];
        
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
    
    public function getClause($clauseId)
    {
        if(key_exists($clauseId, $this->clauses))
            return $this->clauses[$clauseId];
    }
    
    public function getClauseParts()
    {
        return $this->clauseParts;
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
        
        $this->clauseParts = array();
        foreach ($this->clauses as $clauseKey=>$clause)
        {
            if(preg_match($pattern, $clause, $this->clauseParts[$clauseKey]) == 1)
            {
                if($this->clauseParts[$clauseKey][5] != 'IS NULL')
                {
                    $this->newClauses[$clauseKey] = str_replace($this->clauseParts[$clauseKey][4].$this->clauseParts[$clauseKey][5].$this->clauseParts[$clauseKey][6], ":value{$clauseKey}", $clause);
                    $this->stamments[":value{$clauseKey}"] = $this->clauseParts[$clauseKey][5];
                }
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

