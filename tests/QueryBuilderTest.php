<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use PHPUnit\Framework\TestCase;
use MySaasPackage\Support\QueryPart\OrderBy;

final class QueryBuilderTest extends TestCase
{
    public function testSelectSucceessful(): void
    {
        $userCTE = QueryBuilder::create()->select(['id AS user__id', 'name', 'email'])->from('users');
        $profileCTE = QueryBuilder::create()->select(['id AS user__id', 'name', 'email'])->from('profile')->returning(['id AS profile__id', 'user_id', 'address']);
        $query = QueryBuilder::create()
            ->with('user', $userCTE)
            ->with('profile', $profileCTE)
            ->select([
                'u.id AS user__id',
                'u.name AS user__name',
                'u.email AS user__email',
            ])
            ->from('schema.users', 'u')
            ->join('user_profiles', 'up', 'up.user_id = u.id')
            ->where('up.name = :name')
            ->orderBy(['u.id'], OrderBy::DESC)
            ->limit(10)
            ->setParameter('name', 'John');

        $this->assertEqualsIgnoringCase('SELECT id as user__id, name, email FROM users', $userCTE->__toString());
        $this->assertEqualsIgnoringCase('SELECT id as user__id, name, email FROM profile RETURNING id as profile__id, user_id, address', $profileCTE->__toString());
        $this->assertEqualsIgnoringCase('WITH user AS (SELECT id AS user__id, name, email FROM users), profile AS (SELECT id AS user__id, name, email FROM profile RETURNING id AS profile__id, user_id, address) SELECT u.id AS user__id, u.name AS user__name, u.email AS user__email FROM schema.users AS u JOIN user_profiles AS up ON up.user_id = u.id WHERE up.name = \'John\' ORDER BY u.id DESC LIMIT 10', $query->__toString());
    }

    public function testInsertSuccessful(): void
    {
        $query = QueryBuilder::create()
            ->insert('users')
            ->values([
                'name' => '?',
                'email' => '?',
                'phone' => '?',
            ])
            ->setParameter(0, 'John')
            ->setParameter(1, 'alef@gmail.com')
            ->setParameter(2, '+234567890');

        $this->assertEqualsIgnoringCase('INSERT INTO users (name, email, phone) VALUES (\'John\', \'alef@gmail.com\', \'+234567890\')', $query->__toString());
    }

    public function testUpdateSuccessful(): void
    {
        $query = QueryBuilder::create()
            ->update('users')
            ->set([
                'name' => '?',
                'email' => '?',
            ])
            ->where('id = ?')
            ->setParameter(0, 'John')
            ->setParameter(1, 'john@gmail.com')
            ->setParameter(2, 1);

        $this->assertEqualsIgnoringCase('UPDATE users SET name = \'John\', email = \'john@gmail.com\' WHERE id = 1', $query->__toString());
    }
}
