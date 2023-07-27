<?php

declare(strict_types=1);

namespace MySaasPackage\Support\QueryPart\Limit;

use InvalidArgumentException;
use MySaasPackage\Support\QueryPart\DbDriver;
use MySaasPackage\Support\QueryPart\QueryPart;
use MySaasPackage\Support\QueryPart\DrivablePart;

class LimitPart implements QueryPart, DrivablePart
{
    public function __construct(
        public readonly DbDriver $driver,
        public readonly int $limit,
        public readonly int|null $offset = null
    ) {
        if ($limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than 0');
        }

        if ($offset && $offset < 0) {
            throw new InvalidArgumentException('Offset must be greater than or equal to 0');
        }
    }

    public function __toMySQL()
    {
        if (null === $this->offset) {
            return sprintf('LIMIT %d', $this->limit);
        }

        return sprintf('LIMIT %d, %d', $this->offset, $this->limit);
    }

    public function __toPostgresSQL()
    {
        if (null === $this->offset) {
            return sprintf('LIMIT %d', $this->limit);
        }

        return sprintf('LIMIT %d OFFSET %d', $this->limit, $this->offset);
    }

    public function __toString()
    {
        if (DbDriver::MySQL === $this->driver) {
            return $this->__toMySQL();
        }

        if (DbDriver::PostgreSQL === $this->driver) {
            return $this->__toPostgresSQL();
        }

        throw new InvalidArgumentException('Unsupported driver');
    }
}
