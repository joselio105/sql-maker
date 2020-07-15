<?php

namespace src\interfaces;

use entity\EntityInterface;

require_once 'src/interfaces/SqlInterface.php';

interface SqlReadInterface extends SqlInterface
{
    /**
     * Determina as restrições ao fazer a consulta 
     * @param string $where
     */
    public function setWhere(string $where);
    
    /**
     * Determina o campo que ordenará a consulta e se essa ordem será crescente ou decrescente
     * @param string $orderBy
     * @param bool $desc - O padão é falso, ou seja, a consulta será em ordem crescente
     */
    public function setOrder(string $orderBy, bool $desc=false);
    
    /**
     * Limita o número de resultados de uma consulta e o ponto onde essa inicia
     * @param int $limit
     * @param int $offset - O padrão é 0, ou seja, a consuta será exibida a partir do primeiro elemento
     */
    public function setLimit(int $limit, int $offset=0);
    
    /**
     * Une uma tabela com sua rela��o para a consulta
     * @param EntityInterface $entity
     * @param string $on - Rela��o entre as tabelas
     * @param string $joinType - INNER por padr�o
     */
    public function setJoin(EntityInterface $entity, string $on, $joinType='INNER');
    
    /**
     * Concatena dois ou mais campos da consulta
     * @param array $fields
     * @param string $alias
     * @param string $separator - Espaço em branco por padrão
     */
    public function setConcat(array $fields, string $alias, string $separator="' '");
    
    /**
     * Faz uma subconsulta dentro da consulta
     * @param string $query
     * @param string $alias
     */
    public function setSubQuery(string $query, string $alias);
    
    /**
     * Indica que a consulta será uma contagem de elementos
     */
    public function setCount();
    
    /**
     * Indica que a consulta será o somatório de um determinado campo
     * @param string $field
     */
    public function setSum(string $field);
}

