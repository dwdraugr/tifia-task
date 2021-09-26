<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

class DbController extends Controller
{
    /**
     * Make some index for better performance
     * @return int
     */
    public function actionMakeIndex(): int
    {
        $sql = <<<'SQL'
            CREATE INDEX IF NOT EXISTS idx_login ON trades (login);
            CREATE INDEX IF NOT EXISTS idx_client_uid ON accounts (client_uid);
            CREATE INDEX IF NOT EXISTS idx_partner_uid ON users (partner_id);
            CREATE INDEX IF NOT EXISTS idx_client_uid_partner_id ON users (client_uid, partner_id);
SQL;

        $db = \Yii::$app->db;

        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)
                ->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage() . PHP_EOL;
            return ExitCode::IOERR;
        }

        return ExitCode::OK;
    }

    public function actionDropIndex(): int
    {
        $sql = <<<'SQL'
            DROP INDEX IF EXISTS idx_login ON trades;
            DROP INDEX IF EXISTS idx_client_uid ON accounts;
            DROP INDEX IF EXISTS idx_partner_uid ON users;
            DROP INDEX IF EXISTS idx_client_uid_partner_id ON users;
SQL;

        $db = \Yii::$app->db;

        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)
                ->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage() . PHP_EOL;
            return ExitCode::IOERR;
        }

        return ExitCode::OK;
    }
}