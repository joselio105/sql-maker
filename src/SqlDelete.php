<?php

declare(strict_types=1);

namespace src;

require_once 'src/SqlWrite.php';

class SqlDelete extends SqlWrite
{
    protected function setSqlString()
    {
        $this->sqlString = "DELETE FROM {$this->entityObject->getTableName()} WHERE id={$this->getIdValue()}";
    }
    
    protected function validate()
    {
        $this->validId();
    }
}

