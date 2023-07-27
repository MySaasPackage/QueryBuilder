<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use MySaasPackage\Support\QueryPart\StringablePart;
use MySaasPackage\Support\QueryPart\Table\TablePart;

trait JoinModule
{
    protected JoinPartCollection|null $joinPartCollection = null;

    protected function addJoinPartToCollection(JoinPart $join): static
    {
        $this->joinPartCollection ??= new JoinPartCollection();
        $this->joinPartCollection->add($join);

        return $this;
    }

    public function join(StringablePart|string $table, string $alias, string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function leftJoin(StringablePart|string $table, string $alias, string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::LEFT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function rightJoin(StringablePart|string $table, string $alias, string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::RIGHT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function innerJoin(StringablePart|string $table, string $alias, string $condition): static
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
        return $this->joinPartCollection?->__toString() ?? '';
    }
}
