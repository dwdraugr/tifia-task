<?php

namespace app\service\referral;

use app\models\User;
use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;
use Yii;

class ComputingClient
{
    /**
     * @param int $clientUid
     * @return User[]
     */
    public function getReferralsTree(int $clientUid): array
    {
        $sql = <<<'SQL'
        WITH RECURSIVE recurcive_user_search (client_uid, partner_id, fullname, level, ar) AS (
            SELECT client_uid, partner_id, fullname, 0 AS level, cast(client_uid AS varchar(255)) AS ar
            FROM users
            WHERE client_uid = :clientUid
            UNION ALL
            SELECT u.client_uid,
                   u.partner_id,
                   u.fullname,
                   level + 1                                                              AS level,
                   concat(recurcive_user_search.ar, cast(u.client_uid AS varchar(255))) AS ar
            FROM users u
                     JOIN recurcive_user_search ON recurcive_user_search.client_uid = u.partner_id
        )
        SELECT client_uid, partner_id, fullname, level
        FROM recurcive_user_search
        ORDER BY ar
SQL;

        return User::findBySql($sql, [
            ':clientUid' => $clientUid,
        ])
            ->all();
    }

    public function getReferralsSummarizeValue(
        int $clientUid,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): float {
        $sql = <<<'SQL'
            SELECT sum(volume * trades.coeff_cr * trades.coeff_h)
            FROM trades
            WHERE close_time BETWEEN :dateFrom AND :dateTo
              AND login IN (
                SELECT login
                FROM accounts
                         inner join (
                    WITH RECURSIVE recursive_user_call (client_uid, partner_id, fullname, lvl, ar) AS (
                        SELECT client_uid, partner_id, fullname, 0 AS lvl, cast(client_uid AS varchar(255)) AS ar
                        FROM users
                        WHERE client_uid = :clientUid
                        UNION ALL
                        SELECT u.client_uid,
                               u.partner_id,
                               u.fullname,
                               lvl + 1                                                            AS lvl,
                               concat(recursive_user_call.ar, cast(u.client_uid AS varchar(255))) AS ar
                        FROM users u
                                 JOIN recursive_user_call ON recursive_user_call.client_uid = u.partner_id
                    )
                    SELECT *
                    FROM recursive_user_call
                    ORDER BY ar
                ) as superkek USING (client_uid))
SQL;

        return Yii::$app->db
                ->createCommand($sql, [
                ':clientUid' => $clientUid,
                ':dateFrom' => $dateFrom->format('Y-m-d H:i:s'),
                ':dateTo' => $dateTo->format('Y-m-d H:i:s')
            ])
                ->queryScalar() ?? 0.0;
    }

    public function getReferralsProfit(
        int $clientUid,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): float {
        $sql = <<<'SQL'
            SELECT sum(profit)
            FROM trades
            WHERE close_time BETWEEN :dateFrom AND :dateTo
              AND login IN (
                SELECT login
                FROM accounts
                         inner join (
                    WITH RECURSIVE recursive_user_call (client_uid, partner_id, fullname, lvl, ar) AS (
                        SELECT client_uid, partner_id, fullname, 0 AS lvl, cast(client_uid AS varchar(255)) AS ar
                        FROM users
                        WHERE client_uid = :clientUid
                        UNION ALL
                        SELECT u.client_uid,
                               u.partner_id,
                               u.fullname,
                               lvl + 1                                                            AS lvl,
                               concat(recursive_user_call.ar, cast(u.client_uid AS varchar(255))) AS ar
                        FROM users u
                                 JOIN recursive_user_call ON recursive_user_call.client_uid = u.partner_id
                    )
                    SELECT *
                    FROM recursive_user_call
                    ORDER BY ar
                ) as result USING (client_uid))
SQL;

        return Yii::$app->db
                ->createCommand($sql, [
                ':clientUid' => $clientUid,
                ':dateFrom' => $dateFrom->format('Y-m-d H:i:s'),
                ':dateTo' => $dateTo->format('Y-m-d H:i:s')
            ])
                ->queryScalar() ?? 0.0;
    }

    public function getDirectReferralsCount(int $clientUid): int
    {
        return count(User::findOne(['client_uid' => $clientUid])->referrals);
    }

    public function getAllReferralsCount(int $clientUid)
    {
        $sql = <<<'SQL'
            WITH RECURSIVE recurcive_user_search (client_uid) AS (
                SELECT client_uid
                FROM users
                WHERE client_uid = :clientUid
                UNION ALL
                SELECT u.client_uid
                FROM users u
                JOIN recurcive_user_search ON recurcive_user_search.client_uid = u.partner_id
            )
            SELECT count(*)
            FROM recurcive_user_search
            WHERE client_uid != :clientUid
SQL;

        return Yii::$app->db
            ->createCommand($sql, [
            ':clientUid' => $clientUid
        ])
            ->queryScalar();
    }

    public function getReferralsDepth(int $clientUid): int
    {
        $sql = <<<'SQL'
            WITH RECURSIVE recurcive_user_search (level, client_uid) AS (
                SELECT 0 AS level,
                       client_uid
                FROM users
                WHERE client_uid = :clientUid
                UNION ALL
                SELECT level + 1 AS level,
                       u.client_uid
                FROM users u
                         JOIN recurcive_user_search ON recurcive_user_search.client_uid = u.partner_id
            )
            SELECT max(level)
            FROM recurcive_user_search
            WHERE client_uid != :clientUid
SQL;

        return Yii::$app->db
            ->createCommand($sql, [
                'clientUid' => $clientUid,
            ])
            ->queryScalar() ?? 0;
    }
}