<?php

declare(strict_types=1);

namespace MySaasPackage\QueryBuilder;

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder('users');
    }

    private function normalizeSQL(string $sql): string
    {
        // Remove all whitespace (spaces, newlines, tabs)
        $sql = preg_replace('/\s+/', ' ', trim($sql));

        // Convert to lowercase
        $sql = strtolower($sql);

        // Remove spaces around common SQL punctuation
        $sql = preg_replace('/\s*(,|\(|\))\s*/', '$1', $sql);

        // Ensure single space after keywords
        $keywords = ['select', 'from', 'where', 'and', 'or', 'union all', 'group by', 'order by', 'having'];
        foreach ($keywords as $keyword) {
            $sql = preg_replace('/\b' . $keyword . '\s+/', $keyword . ' ', $sql);
        }

        return trim($sql);
    }

    private function assertSQLEquals(string $expected, string $actual): void
    {
        $this->assertEquals(
            $this->normalizeSQL($expected),
            $this->normalizeSQL($actual),
            'SQL queries do not match semantically'
        );
    }

    public function testBasicSelect(): void
    {
        $sql = $this->queryBuilder
            ->select('id', 'name', 'email')
            ->toSQL();

        $this->assertSQLEquals('SELECT id, name, email FROM users', $sql);
    }

    public function testSelectAll(): void
    {
        $sql = $this->queryBuilder
            ->select()
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users', $sql);
    }

    public function testWhereClause(): void
    {
        $sql = $this->queryBuilder
            ->select('*')
            ->where('age > :age', ['age' => 18])
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users WHERE age > :age', $sql);
        $this->assertEquals(['age' => 18], $this->queryBuilder->getParams());
    }

    public function testMultipleWhereClauses(): void
    {
        $sql = $this->queryBuilder
            ->select('*')
            ->where('age > :age', ['age' => 18])
            ->andWhere('status = :status', ['status' => 'active'])
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users WHERE age > :age AND status = :status', $sql);
        $this->assertEquals(['age' => 18, 'status' => 'active'], $this->queryBuilder->getParams());
    }

    public function testOrWhereClause(): void
    {
        $sql = $this->queryBuilder
            ->select('*')
            ->where('age > :age', ['age' => 18])
            ->orWhere('status = :status', ['status' => 'active'])
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users WHERE age > :age OR status = :status', $sql);
        $this->assertEquals(['age' => 18, 'status' => 'active'], $this->queryBuilder->getParams());
    }

    public function testJoinClauses(): void
    {
        $sql = $this->queryBuilder
            ->select('users.*', 'profiles.bio')
            ->join('profiles', 'users.id = profiles.user_id')
            ->toSQL();

        $this->assertSQLEquals('SELECT users.*, profiles.bio FROM users JOIN profiles ON users.id = profiles.user_id', $sql);
    }

    public function testLeftJoin(): void
    {
        $sql = $this->queryBuilder
            ->select('users.*', 'profiles.bio')
            ->leftJoin('profiles', 'users.id = profiles.user_id')
            ->toSQL();

        $this->assertSQLEquals('SELECT users.*, profiles.bio FROM users LEFT JOIN profiles ON users.id = profiles.user_id', $sql);
    }

    public function testGroupBy(): void
    {
        $sql = $this->queryBuilder
            ->select('department', 'COUNT(*) as count')
            ->groupBy('department')
            ->toSQL();

        $this->assertSQLEquals('SELECT department, COUNT(*) as count FROM users GROUP BY department', $sql);
    }

    public function testHaving(): void
    {
        $sql = $this->queryBuilder
            ->select('department', 'COUNT(*) as count')
            ->groupBy('department')
            ->having('COUNT(*) > :min_count', ['min_count' => 5])
            ->toSQL();

        $this->assertSQLEquals('SELECT department, COUNT(*) as count FROM users GROUP BY department HAVING COUNT(*) > :min_count', $sql);
        $this->assertEquals(['min_count' => 5], $this->queryBuilder->getParams());
    }

    public function testOrderBy(): void
    {
        $sql = $this->queryBuilder
            ->select('*')
            ->orderBy('name', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users ORDER BY name ASC, created_at DESC', $sql);
    }

    public function testLimitAndOffset(): void
    {
        $sql = $this->queryBuilder
            ->select('*')
            ->limit(10)
            ->offset(20)
            ->toSQL();

        $this->assertSQLEquals('SELECT * FROM users LIMIT 10 OFFSET 20', $sql);
    }

    public function testWithCTE(): void
    {
        $subQuery = new QueryBuilder('orders');
        $subQuery->select('user_id', 'SUM(amount) as total_amount')
            ->groupBy('user_id');

        $sql = $this->queryBuilder
            ->select('users.*', 'user_totals.total_amount')
            ->with('user_totals', $subQuery)
            ->join('user_totals', 'users.id = user_totals.user_id')
            ->toSQL();

        $expected = 'WITH user_totals AS (SELECT user_id, SUM(amount) as total_amount FROM orders GROUP BY user_id) '
            . 'SELECT users.*, user_totals.total_amount FROM users JOIN user_totals ON users.id = user_totals.user_id';

        $this->assertSQLEquals($expected, $sql);
    }

    public function testComplexQuery(): void
    {
        $sql = $this->queryBuilder
            ->select('users.id', 'users.name', 'COUNT(orders.id) as order_count')
            ->join('orders', 'users.id = orders.user_id')
            ->where('users.status = :status', ['status' => 'active'])
            ->groupBy('users.id', 'users.name')
            ->having('COUNT(orders.id) > :min_orders', ['min_orders' => 5])
            ->orderBy('order_count', 'DESC')
            ->limit(10)
            ->toSQL();

        $expected = 'SELECT users.id, users.name, COUNT(orders.id) as order_count FROM users '
            . 'JOIN orders ON users.id = orders.user_id '
            . 'WHERE users.status = :status '
            . 'GROUP BY users.id, users.name '
            . 'HAVING COUNT(orders.id) > :min_orders '
            . 'ORDER BY order_count DESC '
            . 'LIMIT 10';

        $this->assertSQLEquals($expected, $sql);
        $this->assertEquals(['status' => 'active', 'min_orders' => 5], $this->queryBuilder->getParams());
    }

    public function testComplexRecursiveEventQuery(): void
    {
        $eventGeneratorBase = new QueryBuilder('schema.events');
        $eventGeneratorBase->select(
            'id',
            'uuid',
            'name',
            'description',
            'start_date::DATE',
            'start_date::DATE AS first_event_date',
            'AGE(start_date, start_date) AS first_event_age',
            'start_time',
            'end_time',
            'recurrence_type',
            'recurrence_frequency',
            'occurrence_number',
            'event_category_id',
            'created_at'
        )
        ->where('occurrence_number = :occurrence_number', ['occurrence_number' => 1])
        ->andWhere('AGE(start_date, CURRENT_DATE) < :interval', ['interval' => "INTERVAL '1 year'"]);

        $eventGeneratorRecursive = new QueryBuilder();
        $eventGeneratorRecursive->select(
            'rg.id',
            'rg.uuid',
            'rg.name',
            'rg.description',
            "(CASE\n                WHEN rg.recurrence_frequency = 'daily' THEN rg.start_date + INTERVAL '1 day'\n                WHEN rg.recurrence_frequency = 'weekly' THEN rg.start_date + INTERVAL '1 week'\n                WHEN rg.recurrence_frequency = 'monthly' THEN rg.start_date + INTERVAL '1 month'\n            END)::DATE AS start_date",
            'rg.first_event_date',
            'AGE(rg.start_date, rg.first_event_date) AS first_event_age',
            'rg.start_time',
            'rg.end_time',
            'rg.recurrence_type',
            'rg.recurrence_frequency',
            'rg.occurrence_number + 1 AS occurrence_number',
            'rg.event_category_id',
            'rg.created_at'
        )
        ->from('event_generator rg')
        ->where("rg.recurrence_type = 'recurring'")
        ->andWhere("AGE(rg.start_date, rg.first_event_date) < INTERVAL '1 year'");

        // Create the final events CTE
        $finalEventsQuery = new QueryBuilder();
        $finalEventsQuery->select(
            'gen.id',
            'gen.uuid',
            'COALESCE(uniq.name, gen.name) AS name',
            'COALESCE(uniq.description, gen.description) AS description',
            'COALESCE(uniq.start_date, gen.start_date) AS start_date',
            'gen.first_event_date',
            'gen.first_event_age',
            'COALESCE(uniq.start_time, gen.start_time) AS start_time',
            'COALESCE(uniq.end_time, gen.end_time) AS end_time',
            'COALESCE(uniq.recurrence_type, gen.recurrence_type) AS recurrence_type',
            'COALESCE(uniq.recurrence_frequency, gen.recurrence_frequency) AS recurrence_frequency',
            'COALESCE(uniq.occurrence_number, gen.occurrence_number) AS occurrence_number',
            'gen.event_category_id',
            'uniq.occurrence_number AS unique_occurrence_number',
            'COALESCE(uniq.created_at, gen.created_at) AS created_at'
        )
        ->from('event_generator gen')
        ->leftJoin('schema.events uniq', "gen.uuid = uniq.uuid AND uniq.recurrence_type = 'unique' AND gen.occurrence_number = uniq.occurrence_number");

        // Create the main query
        $mainQuery = new QueryBuilder();

        // Add the CTEs to the main query
        $mainQuery->withRecursive('event_generator', $eventGeneratorBase, $eventGeneratorRecursive)
            ->with('final_events', $finalEventsQuery);

        $mainQuery->select(
            'id',
            'uuid',
            'name',
            'description',
            'start_date',
            'start_time',
            'end_time',
            'recurrence_type',
            'recurrence_frequency',
            'occurrence_number',
            'event_category_id',
            'created_at'
        )
        ->from('final_events')
        ->where('start_date >= :start_date', ['start_date' => '2024-01-01'])
        ->andWhere('start_date <= :end_date', ['end_date' => '2024-12-31'])
        ->orderBy('start_date', 'ASC');

        $expectedSQL = <<<'SQL'
WITH RECURSIVE event_generator AS (
    SELECT id, uuid, name, description, start_date::DATE, start_date::DATE AS first_event_date,
           AGE(start_date, start_date) AS first_event_age, start_time, end_time, recurrence_type,
           recurrence_frequency, occurrence_number, event_category_id, created_at
    FROM schema.events
    WHERE occurrence_number = :occurrence_number AND AGE(start_date, CURRENT_DATE) < :interval
    UNION ALL
    SELECT rg.id, rg.uuid, rg.name, rg.description,
           (CASE
                WHEN rg.recurrence_frequency = 'daily' THEN rg.start_date + INTERVAL '1 day'
                WHEN rg.recurrence_frequency = 'weekly' THEN rg.start_date + INTERVAL '1 week'
                WHEN rg.recurrence_frequency = 'monthly' THEN rg.start_date + INTERVAL '1 month'
            END)::DATE AS start_date,
           rg.first_event_date, AGE(rg.start_date, rg.first_event_date) AS first_event_age,
           rg.start_time, rg.end_time, rg.recurrence_type, rg.recurrence_frequency,
           rg.occurrence_number + 1 AS occurrence_number, rg.event_category_id, rg.created_at
    FROM event_generator rg
    WHERE rg.recurrence_type = 'recurring' AND AGE(rg.start_date, rg.first_event_date) < INTERVAL '1 year'
), final_events AS (
    SELECT gen.id, gen.uuid, COALESCE(uniq.name, gen.name) AS name,
           COALESCE(uniq.description, gen.description) AS description,
           COALESCE(uniq.start_date, gen.start_date) AS start_date, gen.first_event_date,
           gen.first_event_age, COALESCE(uniq.start_time, gen.start_time) AS start_time,
           COALESCE(uniq.end_time, gen.end_time) AS end_time,
           COALESCE(uniq.recurrence_type, gen.recurrence_type) AS recurrence_type,
           COALESCE(uniq.recurrence_frequency, gen.recurrence_frequency) AS recurrence_frequency,
           COALESCE(uniq.occurrence_number, gen.occurrence_number) AS occurrence_number,
           gen.event_category_id, uniq.occurrence_number AS unique_occurrence_number,
           COALESCE(uniq.created_at, gen.created_at) AS created_at
    FROM event_generator gen
    LEFT JOIN schema.events uniq ON gen.uuid = uniq.uuid AND uniq.recurrence_type = 'unique' AND gen.occurrence_number = uniq.occurrence_number
)
SELECT id, uuid, name, description, start_date, start_time, end_time, recurrence_type,
       recurrence_frequency, occurrence_number, event_category_id, created_at
FROM final_events
WHERE start_date >= :start_date AND start_date <= :end_date
ORDER BY start_date ASC
SQL;

        $expectedParams = [
            'occurrence_number' => 1,
            'interval' => "INTERVAL '1 year'",
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ];

        $this->assertSQLEquals($expectedSQL, $mainQuery->toSQL());
        $this->assertEquals($expectedParams, $mainQuery->getParams());
    }
}
