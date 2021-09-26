<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int|null $client_uid
 * @property string|null $email
 * @property string|null $gender
 * @property string|null $fullname
 * @property string|null $country
 * @property string|null $region
 * @property string|null $city
 * @property string|null $address
 * @property int|null $partner_id
 * @property string|null $reg_date
 * @property int|null $status
 * @property string $USER [char(128)]
 * @property int $CURRENT_CONNECTIONS [bigint(20)]
 * @property int $TOTAL_CONNECTIONS [bigint(20)]]
 * @property User[] referrals
 */
class User extends \yii\db\ActiveRecord
{
    public $level;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_uid', 'partner_id', 'status', 'level'], 'integer'],
            [['reg_date'], 'safe'],
            [['email'], 'string', 'max' => 100],
            [['gender'], 'string', 'max' => 5],
            [['fullname'], 'string', 'max' => 150],
            [['country'], 'string', 'max' => 2],
            [['region', 'city'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 200],
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
            'email' => 'Email',
            'gender' => 'Gender',
            'fullname' => 'Fullname',
            'country' => 'Country',
            'region' => 'Region',
            'city' => 'City',
            'address' => 'Address',
            'partner_id' => 'Partner ID',
            'reg_date' => 'Reg Date',
            'status' => 'Status',
            'level' => 'Level'
        ];
    }

    public function getAccounts(): ActiveQuery
    {
        return $this->hasMany(Account::class, ['client_uid' => 'client_uid']);
    }

    public function getPartner(): ActiveQuery
    {
        return $this->hasOne(User::class, ['client_uid' => 'partner_id']);
    }

    public function getReferrals(): ActiveQuery
    {
        return $this->hasMany(User::class, ['partner_id' => 'client_uid']);
    }

}
