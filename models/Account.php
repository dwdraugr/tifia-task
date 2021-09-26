<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "accounts".
 *
 * @property int $id
 * @property int|null $client_uid
 * @property int|null $login
 * @property string $USER [char(128)]
 * @property string $HOST [char(60)]
 * @property int $CURRENT_CONNECTIONS [bigint(20)]
 * @property int $TOTAL_CONNECTIONS [bigint(20)]
 */
class Account extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accounts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_uid', 'login'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_uid' => 'Client Uid',
            'login' => 'Login',
        ];
    }

    public function getTrades()
    {
        return $this->hasMany(Trade::class, ['login' => 'login']);
    }
}
