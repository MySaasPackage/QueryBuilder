# QueryBuilder

A fluent SQL query builder for PHP that supports complex queries including CTEs, recursive CTEs, and various SQL operations.

## Installation

```bash
composer require mysaaspackage/querybuilder
```

## Basic Usage

### SELECT Queries

```php
use MySaasPackage\QueryBuilder\QueryBuilder;

// Basic select
$query = new QueryBuilder('users');
$query->select('id', 'name', 'email');
// SELECT id, name, email FROM users

// Select with where clause
$query = new QueryBuilder('users');
$query->select('*')
    ->where('age > :age', ['age' => 18]);
// SELECT * FROM users WHERE age > :age

// Select with multiple conditions
$query = new QueryBuilder('users');
$query->select('*')
    ->where('age > :age', ['age' => 18])
    ->andWhere('status = :status', ['status' => 'active'])
    ->orWhere('is_admin = :is_admin', ['is_admin' => true]);
// SELECT * FROM users WHERE age > :age AND status = :status OR is_admin = :is_admin

// Select with joins
$query = new QueryBuilder('users');
$query->select('users.*', 'profiles.bio')
    ->join('profiles', 'p', 'users.id = p.user_id')
    ->leftJoin('addresses', 'a', 'users.id = a.user_id');
// SELECT users.*, profiles.bio FROM users JOIN profiles AS p ON users.id = p.user_id LEFT JOIN addresses AS a ON users.id = a.user_id

// Select with group by and having
$query = new QueryBuilder('orders');
$query->select('user_id', 'COUNT(*) as order_count')
    ->groupBy('user_id')
    ->having('COUNT(*) > :min_orders', ['min_orders' => 5]);
// SELECT user_id, COUNT(*) as order_count FROM orders GROUP BY user_id HAVING COUNT(*) > :min_orders

// Select with order by and limit
$query = new QueryBuilder('products');
$query->select('*')
    ->orderBy('price', 'DESC')
    ->orderBy('name')
    ->limit(10)
    ->offset(20);
// SELECT * FROM products ORDER BY price DESC, name LIMIT 10 OFFSET 20
```

### Common Table Expressions (CTEs)

```php
// Basic CTE
$subQuery = new QueryBuilder('orders');
$subQuery->select('user_id', 'SUM(amount) as total_amount')
    ->groupBy('user_id');

$query = new QueryBuilder('users');
$query->with('user_totals', $subQuery)
    ->select('users.*', 'user_totals.total_amount')
    ->join('user_totals', 'ut', 'users.id = ut.user_id');
// WITH user_totals AS (SELECT user_id, SUM(amount) as total_amount FROM orders GROUP BY user_id)
// SELECT users.*, user_totals.total_amount FROM users JOIN user_totals AS ut ON users.id = ut.user_id

// Recursive CTE (for hierarchical data)
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
// WITH RECURSIVE category_tree AS (
//     SELECT id, name, parent_id FROM categories WHERE parent_id IS NULL
//     UNION ALL
//     SELECT c.id, c.name, c.parent_id FROM categories AS c
//     JOIN category_tree AS ct ON c.parent_id = ct.id
// )
// SELECT * FROM category_tree
```

### INSERT, UPDATE, and DELETE Operations

```php
// INSERT
$query = new QueryBuilder();
$query->insert('users')
    ->values([
        'name' => ':name',
        'email' => ':email',
        'age' => ':age'
    ])
    ->setParameter('name', 'John Doe')
    ->setParameter('email', 'john@example.com')
    ->setParameter('age', 30);
// INSERT INTO users (name, email, age) VALUES (:name, :email, :age)

// UPDATE
$query = new QueryBuilder();
$query->update('users')
    ->set([
        'name' => ':new_name',
        'email' => ':new_email'
    ])
    ->where('id = :id', ['id' => 1])
    ->setParameter('new_name', 'Jane Doe')
    ->setParameter('new_email', 'jane@example.com');
// UPDATE users SET name = :new_name, email = :new_email WHERE id = :id

// DELETE
$query = new QueryBuilder();
$query->delete('users')
    ->where('id = :id', ['id' => 1]);
// DELETE FROM users WHERE id = :id
```

### Complex Queries with Subqueries

```php
// Subquery in WHERE clause
$subQuery = new QueryBuilder('orders');
$subQuery->select('user_id')
    ->where('total > :min_total', ['min_total' => 1000]);

$query = new QueryBuilder('users');
$query->select('*')
    ->where('id IN :user_ids', ['user_ids' => $subQuery]);
// SELECT * FROM users WHERE id IN (SELECT user_id FROM orders WHERE total > :min_total)

// Subquery in SELECT clause
$avgQuery = new QueryBuilder('products');
$avgQuery->select('AVG(price)');

$query = new QueryBuilder('products');
$query->select('name', 'price')
    ->where('price > :avg_price', ['avg_price' => $avgQuery]);
// SELECT name, price FROM products WHERE price > (SELECT AVG(price) FROM products)
```

## API Reference

### Query Construction Methods

- `select(...$columns): self` - Set columns to select
- `from(string $table, ?string $alias = null): self` - Set the main table
- `where(string $condition, array $params = []): self` - Add a WHERE condition
- `andWhere(string $condition, array $params = []): self` - Add an AND WHERE condition
- `orWhere(string $condition, array $params = []): self` - Add an OR WHERE condition
- `join(string $table, string $alias, string $condition): self` - Add an INNER JOIN
- `leftJoin(string $table, string $alias, string $condition): self` - Add a LEFT JOIN
- `rightJoin(string $table, string $alias, string $condition): self` - Add a RIGHT JOIN
- `groupBy(string ...$columns): self` - Add GROUP BY clauses
- `having(string $condition, array $params = []): self` - Add a HAVING condition
- `orderBy(string $column, ?string $direction = null): self` - Add an ORDER BY clause
- `limit(int $limit): self` - Set the LIMIT
- `offset(int $offset): self` - Set the OFFSET

### CTE Methods

- `with(string $name, QueryBuilder $query, array $columns = [], bool $recursive = false): self` - Add a CTE
- `withRecursive(string $name, QueryBuilder $baseQuery, QueryBuilder $recursiveQuery, array $columns = []): self` - Add a recursive CTE

### DML Operations

- `insert(string $table): self` - Start an INSERT query
- `values(array $values): self` - Set values for INSERT
- `update(string $table): self` - Start an UPDATE query
- `set(array $values): self` - Set values for UPDATE
- `delete(string $table): self` - Start a DELETE query

### Parameter Methods

- `setParameter(string|int $key, mixed $value): self` - Set a parameter value
- `getParams(): array` - Get all parameters

### Finalization Methods

- `toSQL(): string` - Get the final SQL query 