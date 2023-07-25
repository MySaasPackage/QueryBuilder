<?php

declare(strict_types=1);

namespace MySaasPackage\Support;

use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{
    public function testSucceessful()
    {
        $userCTE = (new QueryBuilder())
                ->select([
                    'id AS user__id',
                    'name',
                    'email',
                ])
                ->from('users');

        $profileCTE = (new QueryBuilder())
                ->select([
                    'id AS user__id',
                    'name',
                    'email',
                ])
                ->from('profile')
                ->returning([
                    'id AS profile__id',
                    'user_id',
                    'address',
                ]);

        $query = (new QueryBuilder())
            ->with('user', $userCTE)
            ->with('profile', $profileCTE)
            ->select([
                'id AS user__id',
                'name',
                'email',
            ])
            ->from('users')
            ->join('user_profiles', 'user_profiles.user_id = users.id')
            ->where('name = \'John\'')
            ->groupBy([
                'id',
                'name',
                'email',
            ])
            ->orderBy([
                'id',
                'name',
                'email',
            ])
            ->orderBy([
                'id',
                'name',
                'email',
            ], 'DESC')
            ->having('COUNT(*) > 1')
            ->limit(10);

        $sql = "WITH user AS (SELECT id as user__id, name, email FROM users ), profile AS (SELECT id as user__id, name, email FROM profile  RETURNING id as profile__id, user_id, address) SELECT id as user__id, name, email FROM users WHERE name = 'John' ORDER BY id, name, email ASC id, name, email DESC GROUP BY id, name, email HAVING COUNT(*) > 1 LIMIT 10";

        $this->assertEqualsIgnoringCase($sql, $query->__toString());
    }
}
