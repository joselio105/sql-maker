# SQL Maker
Retorna uma string com o código SQL relativo aos critérios passados
## Classes
>### SqlRead
>> **Métodos Públicos**
>>> **__construct**(EntityInterface $entity) : void

>>> **setWhere**(string $where) : void

>>> **setOrder**(string $orderBy, boolean $desc) : void

>>> **setLimit**(int $limit, int $offset) : void

>>> **setJoin**(EntityInterface $entity, string $on, string $tableAlias, string $joinTyoe) : void

>>> **setConcat**(string[] $fields, string $alias, string $separator) : void

>>> **setSubSql**(string $sql, string $alias) : void

>>> **setSum**(string $field) : void

>>> **setCount**(void) : void

>>> **__toString**(void) : string

>### SqlCreate
>### SqlDelete
>### SqlUptade
>### SqlWhere
## Interfaces