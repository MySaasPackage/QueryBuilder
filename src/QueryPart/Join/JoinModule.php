<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use MySaasPackage\Support\QueryPart\TablePart;

trait JoinModule
{
    protected JoinPartCollection|null $joinPartCollection = null;

    protected function addJoinPartToCollection(JoinPart $join): self
    {
        $this->joinPartCollection ??= new JoinPartCollection();
        $this->joinPartCollection->add($join);

        return $this;
    }

    public function join(string $table, string $alias, string $condition): self
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function leftJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::LEFT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function rightJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::RIGHT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function innerJoin(string $table, string $alias, string $condition): self
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::INNER_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    protected function __toJoin(): string
    {
        return $this->joinPartCollection->__toString();
    }
}
