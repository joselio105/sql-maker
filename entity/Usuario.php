<?php

declare(strict_types=1);

namespace entity;

class Usuario
{
    private $id;
    private $nome;
    private $sobrenome;
    private $nascimento;
    private $cargo;
    
    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getSobrenome()
    {
        return $this->sobrenome;
    }

    public function getNascimento()
    {
        return $this->nascimento;
    }
    
    public function getCargo()
    {
        return $this->cargo;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    public function setSobrenome(string $sobrenome)
    {
        $this->sobrenome = $sobrenome;
    }

    public function setNascimento(string $nascimento)
    {
        $this->nascimento = $nascimento;
    }
    
    public function setCargo(int $cargo)
    {
        $this->cargo = $cargo;
    }
}
