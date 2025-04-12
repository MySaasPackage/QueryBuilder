<?php

declare(strict_types=1);

namespace MySaasPackage\QueryBuilder;

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    private function normalizeSQL(string $sql): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($sql)));
    }

    private function assertSQLEquals(string $expected, string $actual): void
    {
        $this->assertEquals(
            $this->normalizeSQL($expected),
            $this->normalizeSQL($actual),
            'SQL queries do not match semantically'
        );
    }

    public function testBasicQueryOperations(): void
    {
        // Test basic select
        $this->assertSQLEquals(
            'SELECT id, name, email FROM users',
            (new QueryBuilder('users'))->select('id', 'name', 'email')->toSQL()
        );

        // Test select with where
        $this->assertSQLEquals(
            'SELECT * FROM users WHERE age > :age',
            (new QueryBuilder('users'))->select('*')->where('age > :age', ['age' => 18])->toSQL()
        );

        // Test multiple conditions
        $this->assertSQLEquals(
            'SELECT * FROM users WHERE age > :age AND status = :status OR is_admin = :is_admin',
            (new QueryBuilder('users'))
                ->select('*')
                ->where('age > :age', ['age' => 18])
                ->andWhere('status = :status', ['status' => 'active'])
                ->orWhere('is_admin = :is_admin', ['is_admin' => true])
                ->toSQL()
        );

        // Test joins
        $this->assertSQLEquals(
            'SELECT users.*, profiles.bio FROM users JOIN profiles AS p ON users.id = p.user_id LEFT JOIN addresses AS a ON users.id = a.user_id',
            (new QueryBuilder('users'))
                ->select('users.*', 'profiles.bio')
                ->join('profiles', 'p', 'users.id = p.user_id')
                ->leftJoin('addresses', 'a', 'users.id = a.user_id')
                ->toSQL()
        );

        // Test group by and having
        $this->assertSQLEquals(
            'SELECT user_id, COUNT(*) as order_count FROM orders GROUP BY user_id HAVING COUNT(*) > :min_orders',
            (new QueryBuilder('orders'))
                ->select('user_id', 'COUNT(*) as order_count')
                ->groupBy('user_id')
                ->having('COUNT(*) > :min_orders', ['min_orders' => 5])
                ->toSQL()
        );

        // Test order by and limit
        $this->assertSQLEquals(
            'SELECT * FROM products ORDER BY price DESC, name LIMIT 10 OFFSET 20',
            (new QueryBuilder('products'))
                ->select('*')
                ->orderBy('price', 'DESC')
                ->orderBy('name')
                ->limit(10)
                ->offset(20)
                ->toSQL()
        );
    }

    public function testCTEs(): void
    {
        // Test basic CTE
        $subQuery = new QueryBuilder('orders');
        $subQuery->select('user_id', 'SUM(amount) as total_amount')
            ->groupBy('user_id');

        $query = new QueryBuilder('users');
        $query->with('user_totals', $subQuery)
            ->select('users.*', 'user_totals.total_amount')
            ->join('user_totals', 'ut', 'users.id = ut.user_id');

        $this->assertSQLEquals(
            'WITH user_totals AS (SELECT user_id, SUM(amount) as total_amount FROM orders GROUP BY user_id) SELECT users.*, user_totals.total_amount FROM users JOIN user_totals AS ut ON users.id = ut.user_id',
            $query->toSQL()
        );

        // Test recursive CTE
        $baseQuery = new QueryBuilder('categories');
        $baseQuery->select('id', 'name', 'parent_id')
            ->where('parent_id IS NULL');

        $recursiveQuery = new QueryBuilder();
        $recursiveQuery->select('c.id', 'c.name', 'c.parent_id')
            ->from('categories', 'c')
            ->join('category_tree', 'ct', 'c.parent_id = ct.id');

        $query = new QueryBuilder();
        $query->withRecursive('category_tree', $baseQuery, $recursiveQuery)
            ->select('*')
            ->from('category_tree');

        $this->assertSQLEquals(
            'WITH RECURSIVE category_tree AS (SELECT id, name, parent_id FROM categories WHERE parent_id IS NULL UNION ALL SELECT c.id, c.name, c.parent_id FROM categories AS c JOIN category_tree AS ct ON c.parent_id = ct.id) SELECT * FROM category_tree',
            $query->toSQL()
        );
    }

    public function testDMLOperations(): void
    {
        // Test INSERT
        $query = new QueryBuilder();
        $query->insert('users')
            ->values([
                'name' => ':name',
                'email' => ':email',
                'age' => ':age',
            ])
            ->setParameter('name', 'John Doe')
            ->setParameter('email', 'john@example.com')
            ->setParameter('age', 30);

        $this->assertSQLEquals(
            'INSERT INTO users (name, email, age) VALUES (:name, :email, :age)',
            $query->toSQL()
        );

        // Test UPDATE
        $query = new QueryBuilder();
        $query->update('users')
            ->set([
                'name' => ':new_name',
                'email' => ':new_email',
            ])
            ->where('id = :id', ['id' => 1])
            ->setParameter('new_name', 'Jane Doe')
            ->setParameter('new_email', 'jane@example.com');

        $this->assertSQLEquals(
            'UPDATE users SET name = :new_name, email = :new_email WHERE id = :id',
            $query->toSQL()
        );

        // Test DELETE
        $query = new QueryBuilder();
        $query->delete('users')
            ->where('id = :id', ['id' => 1]);

        $this->assertSQLEquals(
            'DELETE FROM users WHERE id = :id',
            $query->toSQL()
        );
    }

    public function testSubqueries(): void
    {
        // Test subquery in WHERE clause
        $subQuery = new QueryBuilder('orders');
        $subQuery->select('user_id')
            ->where('total > :min_total', ['min_total' => 1000]);

        $query = new QueryBuilder('users');
        $query->select('*')
            ->where('id IN :user_ids', ['user_ids' => $subQuery]);

        $this->assertSQLEquals(
            'SELECT * FROM users WHERE id IN (SELECT user_id FROM orders WHERE total > :min_total)',
            $query->toSQL()
        );

        // Test subquery in SELECT clause
        $avgQuery = new QueryBuilder('products');
        $avgQuery->select('AVG(price)');

        $query = new QueryBuilder('products');
        $query->select('name', 'price')
            ->where('price > :avg_price', ['avg_price' => $avgQuery]);

        $this->assertSQLEquals(
            'SELECT name, price FROM products WHERE price > (SELECT AVG(price) FROM products)',
            $query->toSQL()
        );
    }

    public function testParameterHandling(): void
    {
        $query = new QueryBuilder('users');
        $query->select('*')
            ->where('id = :id', ['id' => 1])
            ->setParameter('name', 'John Doe');

        $this->assertEquals(
            ['id' => 1, 'name' => 'John Doe'],
            $query->getParams()
        );

        // Test array parameters
        $query = new QueryBuilder('users');
        $query->select('*')
            ->where('id IN :ids', ['ids' => [1, 2, 3]]);

        $this->assertSQLEquals(
            'SELECT * FROM users WHERE id IN (:ids)',
            $query->toSQL()
        );
    }
}
