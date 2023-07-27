<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Join;

use Stringable;
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

    public function join(Stringable|string $table, string $alias, Stringable|string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function leftJoin(Stringable|string $table, string $alias, Stringable|string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::LEFT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function rightJoin(Stringable|string $table, string $alias, Stringable|string $condition): static
    {
        $this->addJoinPartToCollection(new JoinPart(
            type: Join::RIGHT_JOIN,
            table: new TablePart($table, $alias),
            condition: $condition
        ));

        return $this;
    }

    public function innerJoin(Stringable|string $table, string $alias, Stringable|string $condition): static
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
