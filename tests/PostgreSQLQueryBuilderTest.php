<?php

declare(strict_types=1);

namespace MySaasPackage;

use PHPUnit\Framework\TestCase;

final class PostgreSQLQueryBuilderTest extends TestCase
{
    public function testSelect(): void
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

    public function testSelectWithWhereInSubSelect(): void
    {
        $subQuery = QueryBuilder::postgres()
            ->select(['order_id'])
            ->from('order_items')
            ->where('items_name = :name')
            ->setParameter('name', 'Widget');

        $query = QueryBuilder::postgres()
            ->select(['*'])
            ->from('orders')
            ->where('orders_id IN :orders')
            ->setParameter('orders', $subQuery);

        $this->assertEquals('SELECT * FROM orders WHERE orders_id IN (SELECT order_id FROM order_items WHERE items_name = \'Widget\')', $query->__toString());
    }

    public function testSelectWithSubSelect(): void
    {
        $avgRateQuery = QueryBuilder::postgres()
            ->select(['AVG(rate)'])
            ->from('film');

        $query = QueryBuilder::postgres()
            ->select(['id', 'title', 'description', 'rate'])
            ->from('film')
            ->where('rate > :rate')
            ->setParameter('rate', $avgRateQuery);

        $this->assertEquals('SELECT id, title, description, rate FROM film WHERE rate > (SELECT AVG(rate) FROM film)', $query->__toString());
    }

    public function testSelectWithCommonTableExpressions(): void
    {
        $insert = QueryBuilder::postgres()
            ->insert('management.subscriptions')
            ->values([
                'uuid' => ':uuid',
                'first_name' => ':first_name',
                'last_name' => ':last_name',
                'age' => ':age',
                'email' => ':email',
                'phone' => ':phone',
            ])
            ->setParameter('uuid', '69e5e669-910a-4e8c-9529-7142b1ae0655')
            ->setParameter('first_name', 'John')
            ->setParameter('last_name', 'Doe')
            ->setParameter('age', 28)
            ->setParameter('email', 'john@gmail.com')
            ->setParameter('phone', '+11234567890');

        $query = QueryBuilder::postgres()
            ->with('INSERTED_SUBSCRIPTION', $insert)
            ->select([
                's.id AS subscription__id',
                's.uuid AS subscription__uuid',
                's.first_name AS subscription__first_name',
                's.last_name AS subscription__last_name',
                's.email AS subscription__email',
                's.phone AS subscription__phone',
            ])
            ->from('INSERTED_SUBSCRIPTION', 's');

        $this->assertEquals('WITH INSERTED_SUBSCRIPTION AS (INSERT INTO management.subscriptions (uuid, first_name, last_name, age, email, phone) VALUES (\'69e5e669-910a-4e8c-9529-7142b1ae0655\', \'John\', \'Doe\', 28, \'john@gmail.com\', \'+11234567890\')) SELECT s.id AS subscription__id, s.uuid AS subscription__uuid, s.first_name AS subscription__first_name, s.last_name AS subscription__last_name, s.email AS subscription__email, s.phone AS subscription__phone FROM INSERTED_SUBSCRIPTION AS s', $query->__toString());
    }

    public function testSelectWithLimit(): void
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

    public function testSelectWithOrderBy(): void
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
            ->orderBy('s.id');

        $this->assertEquals('SELECT s.id AS subscription__id, s.uuid AS subscription__uuid, s.first_name AS subscription__first_name, s.last_name AS subscription__last_name, s.email AS subscription__email, s.phone AS subscription__phone FROM management.subscriptions AS s ORDER BY s.id', $query->__toString());
    }

    public function testSelectWithOrderByNullFirst(): void
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

    public function testSelectWithHaving(): void
    {
        $query = QueryBuilder::postgres()
            ->select([
                'o.customer_id AS orders__customer__id',
                'SUM(o.total_amount) AS orders__total_sales',
            ])
            ->from('shop.orders', 'o')
            ->groupBy('o.customer_id')
            ->having('SUM(o.total_amount) > :total_sales')
            ->setParameter('total_sales', 1000.1);

        $this->assertEquals('SELECT o.customer_id AS orders__customer__id, SUM(o.total_amount) AS orders__total_sales FROM shop.orders AS o GROUP BY o.customer_id HAVING SUM(o.total_amount) > 1000.1', $query->__toString());
    }

    public function testInsert(): void
    {
        $query = QueryBuilder::postgres()
            ->insert('management.subscriptions')
            ->values([
                'uuid' => ':uuid',
                'first_name' => ':first_name',
                'last_name' => ':last_name',
                'email' => ':email',
                'phone' => ':phone',
            ])
            ->setParameter('uuid', '69e5e669-910a-4e8c-9529-7142b1ae0655')
            ->setParameter('first_name', 'John')
            ->setParameter('last_name', 'Doe')
            ->setParameter('email', 'john@gmail.com')
            ->setParameter('phone', '+11234567890');

        $this->assertEquals('INSERT INTO management.subscriptions (uuid, first_name, last_name, email, phone) VALUES (\'69e5e669-910a-4e8c-9529-7142b1ae0655\', \'John\', \'Doe\', \'john@gmail.com\', \'+11234567890\')', $query->__toString());
    }

    public function testInsertWithNoParams(): void
    {
        $query = QueryBuilder::postgres()
            ->insert('management.subscriptions')
            ->values([
                'uuid' => '?',
                'first_name' => '?',
                'last_name' => '?',
                'email' => '?',
                'phone' => '?',
            ]);

        $this->assertEquals('INSERT INTO management.subscriptions (uuid, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?)', $query->__toString());
    }

    public function testUpdateWithNoParams(): void
    {
        $query = QueryBuilder::postgres()
            ->update('management.subscriptions')
            ->set([
                'uuid' => '?',
                'first_name' => '?',
                'last_name' => '?',
                'email' => '?',
                'phone' => '?',
            ]);

        $this->assertEquals('UPDATE management.subscriptions SET uuid = ?, first_name = ?, last_name = ?, email = ?, phone = ?', $query->__toString());
    }

    public function testDelete(): void
    {
        $query = QueryBuilder::postgres()
            ->delete('management.subscriptions')
            ->where('uuid = ?')
            ->setParameter(0, '69e5e669-910a-4e8c-9529-7142b1ae0655');

        $this->assertEquals('DELETE FROM management.subscriptions WHERE uuid = \'69e5e669-910a-4e8c-9529-7142b1ae0655\'', $query->__toString());
    }

    public function testDeleteWithWhereIn(): void
    {
        $query = QueryBuilder::postgres()
            ->delete('management.subscriptions')
            ->where('uuid IN ?')
            ->setParameter(0, ['69e5e669-910a-4e8c-9529-7142b1ae0655', '69e5e669-910a-4e8c-9529-7142b1ae0656']);

        $this->assertEquals('DELETE FROM management.subscriptions WHERE uuid IN (\'69e5e669-910a-4e8c-9529-7142b1ae0655\', \'69e5e669-910a-4e8c-9529-7142b1ae0656\')', $query->__toString());
    }

    public function testComplexQueryWithCTEs(): void
    {
        $insertTenantQuery = QueryBuilder::postgres()
            ->insert('abraham.tenants')
            ->values([
                'schema' => ':tenant_schema',
                'uuid' => ':tenant_uuid',
            ])
            ->returning(['*']);

        $insertUserQuery = QueryBuilder::postgres()
            ->insert('abraham.users')
            ->values([
                'uuid' => ':user_uuid',
                'email' => ':user_email',
                'phone' => ':user_phone',
                'hash' => ':user_hash',
                'tenant_id' => QueryBuilder::postgres()->select(['id'])->from('INSERTED_TENANT'),
            ])
            ->returning(['*']);

        $updateTenantWithOwner = QueryBuilder::postgres()
            ->update('abraham.tenants')
            ->set(['user_owner_id' => QueryBuilder::postgres()->select(['id'])->from('INSERTED_USER')])
            ->where('id = :tenant_id')
            ->setParameter('tenant_id', QueryBuilder::postgres()->select(['id'])->from('INSERTED_TENANT'))
            ->returning(['*']);

        $selectUerTenantQuery = QueryBuilder::postgres()
            ->with('INSERTED_TENANT', $insertTenantQuery)
            ->with('INSERTED_USER', $insertUserQuery)
            ->with('UPDATED_TENANT', $updateTenantWithOwner->__toString())
            ->with('INSERTED_USER', $insertUserQuery)
            ->select([
                't.id AS tenant__id',
                't.uuid AS tenant__uuid',
                't.schema AS tenant__schema',
                't.created_at AS tenant__created_at',
                'u.id AS user__id',
                'u.uuid AS user__uuid',
                'u.email AS user__email',
                'u.phone AS user__phone',
                'u.hash AS user__hash',
                'u.created_at AS user__created_at',
            ])
            ->from('INSERTED_USER AS u')
            ->leftJoin('INSERTED_TENANT', 't', 't.id = u.tenant_id');

        $this->assertEquals('WITH INSERTED_TENANT AS (INSERT INTO abraham.tenants (schema, uuid) VALUES (:tenant_schema, :tenant_uuid) RETURNING *), INSERTED_USER AS (INSERT INTO abraham.users (uuid, email, phone, hash, tenant_id) VALUES (:user_uuid, :user_email, :user_phone, :user_hash, (SELECT id FROM INSERTED_TENANT)) RETURNING *), UPDATED_TENANT AS (UPDATE abraham.tenants SET user_owner_id = (SELECT id FROM INSERTED_USER) WHERE id = (SELECT id FROM INSERTED_TENANT) RETURNING *), INSERTED_USER AS (INSERT INTO abraham.users (uuid, email, phone, hash, tenant_id) VALUES (:user_uuid, :user_email, :user_phone, :user_hash, (SELECT id FROM INSERTED_TENANT)) RETURNING *) SELECT t.id AS tenant__id, t.uuid AS tenant__uuid, t.schema AS tenant__schema, t.created_at AS tenant__created_at, u.id AS user__id, u.uuid AS user__uuid, u.email AS user__email, u.phone AS user__phone, u.hash AS user__hash, u.created_at AS user__created_at FROM INSERTED_USER AS u LEFT JOIN INSERTED_TENANT AS t ON t.id = u.tenant_id', $selectUerTenantQuery->__toString());
    }
}
