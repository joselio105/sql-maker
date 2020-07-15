<?php

declare(strict_types=1);

namespace entity;

class Cargo
{
    private $id;
    private $nome;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getNome()
    {
        return $this->nome;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
    }
    
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }
    
}

