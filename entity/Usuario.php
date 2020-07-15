<?php

namespace entity;

class Usuario
{
    private $id;
    private $nome;
    private $sobrenome;
    private $nascimento;
    
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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setSobrenome($sobrenome)
    {
        $this->sobrenome = $sobrenome;
    }

    public function setNascimento($nascimento)
    {
        $this->nascimento = $nascimento;
    }
    
}
