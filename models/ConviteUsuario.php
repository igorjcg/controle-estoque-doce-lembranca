<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ConviteUsuario extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%convite_usuario}}';
    }

    public function rules(): array
    {
        return [
            [['token', 'criado_por', 'criado_em', 'expira_em'], 'required'],
            [['criado_por', 'criado_em', 'expira_em', 'usado', 'usado_por'], 'integer'],
            [['token'], 'string', 'max' => 64],
            [['token'], 'unique'],
            [['criado_por'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['criado_por' => 'id']],
            [['usado_por'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['usado_por' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'criado_por' => 'Criado por',
            'criado_em' => 'Criado em',
            'expira_em' => 'Expira em',
            'usado' => 'Usado',
            'usado_por' => 'Usado por',
        ];
    }

    public function isExpirado(): bool
    {
        return (int) $this->expira_em < time();
    }

    public function isUsado(): bool
    {
        return (int) $this->usado === 1;
    }

    public function getCriadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'criado_por']);
    }

    public function getUsadoPor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'usado_por']);
    }
}
