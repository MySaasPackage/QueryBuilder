<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{
    public function testSucceessful()
    {
        $userCTE = QueryBuilder::create()->select(['id AS user__id', 'name', 'email'])->from('users');
        $profileCTE = QueryBuilder::create()->select(['id AS user__id', 'name', 'email'])->from('profile')->returning(['id AS profile__id', 'user_id', 'address']);

        $query = QueryBuilder::create()
            ->with('user', $userCTE)
            ->with('profile', $profileCTE)
            ->select(['id AS user__id', 'name', 'email'])
            ->from('users')
            ->join('user_profiles', 'user_profiles.user_id = users.id')
            ->where('name = "John"')
            ->groupBy(['id', 'name', 'email'])
            ->orderBy(['id', 'name', 'email'])
            ->orderBy(['id', 'name', 'email'], 'DESC')
            ->having('COUNT(*) > 1')
            ->limit(10);

        $this->assertEqualsIgnoringCase('SELECT id as user__id, name, email FROM users', $userCTE->__toString());
        $this->assertEqualsIgnoringCase('SELECT id as user__id, name, email FROM profile RETURNING id as profile__id, user_id, address', $profileCTE->__toString());
        $this->assertEqualsIgnoringCase('WITH user AS (SELECT id as user__id, name, email FROM users), profile AS (SELECT id as user__id, name, email FROM profile RETURNING id as profile__id, user_id, address) SELECT id as user__id, name, email FROM users JOIN user_profiles ON user_profiles.user_id = users.id WHERE name = \'John\' ORDER BY id, name, email ASC id, name, email DESC GROUP BY id, name, email HAVING COUNT(*) > 1 LIMIT 10', $query->__toString());
    }
}
