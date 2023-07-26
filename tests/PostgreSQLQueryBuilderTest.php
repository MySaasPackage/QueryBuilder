<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use PHPUnit\Framework\TestCase;

final class PostgreSQLQueryBuilderTest extends TestCase
{
    public function testSelectSucceessful(): void
    {
        $query = QueryBuilder::postgres()
            ->select([
                's.id AS subscription__id',
                's.uuid AS subscription__uuid',
                's.first_name AS subscription__first_name',
                's.last_name AS subscription__last_name',
                's.email AS subscription__email',
                's.phone AS subscription__phone',
            ])
            ->from('management.subscriptions', 's')
            ->where('s.uuid = :uuid')
            ->setParameter('uuid', '69e5e669-910a-4e8c-9529-7142b1ae0655');

        $this->assertEquals('SELECT s.id AS subscription__id, s.uuid AS subscription__uuid, s.first_name AS subscription__first_name, s.last_name AS subscription__last_name, s.email AS subscription__email, s.phone AS subscription__phone FROM management.subscriptions AS s WHERE s.uuid = \'69e5e669-910a-4e8c-9529-7142b1ae0655\'', $query->__toString());
    }

    public function testSelectLimitSucceessful(): void
    {
        $query = QueryBuilder::postgres()
            ->select([
                's.id AS subscription__id',
                's.uuid AS subscription__uuid',
                's.first_name AS subscription__first_name',
                's.last_name AS subscription__last_name',
                's.email AS subscription__email',
                's.phone AS subscription__phone',
            ])
            ->from('management.subscriptions', 's')
            ->limit(10, 10);

        $this->assertEquals('SELECT s.id AS subscription__id, s.uuid AS subscription__uuid, s.first_name AS subscription__first_name, s.last_name AS subscription__last_name, s.email AS subscription__email, s.phone AS subscription__phone FROM management.subscriptions AS s LIMIT 10 OFFSET 10', $query->__toString());
    }

    public function testSelectOrderBySucceessful(): void
    {
        $query = QueryBuilder::postgres()
            ->select([
                's.id AS subscription__id',
                's.uuid AS subscription__uuid',
                's.first_name AS subscription__first_name',
                's.last_name AS subscription__last_name',
                's.email AS subscription__email',
                's.phone AS subscription__phone',
            ])
            ->from('management.subscriptions', 's')
            ->orderBy('s.id', 'ASC NULLS FIRST');

        $this->assertEquals('SELECT s.id AS subscription__id, s.uuid AS subscription__uuid, s.first_name AS subscription__first_name, s.last_name AS subscription__last_name, s.email AS subscription__email, s.phone AS subscription__phone FROM management.subscriptions AS s ORDER BY s.id ASC NULLS FIRST', $query->__toString());
    }
}
