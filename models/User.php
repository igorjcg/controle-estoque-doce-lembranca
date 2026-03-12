<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public ?string $password = null;
    public ?string $confirmar_password = null;

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['username','email'], 'trim'],
            [['username','email'], 'required'],
            [['password', 'confirmar_password'], 'required', 'on' => 'create'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'email'], 'string', 'max' => 150],
            [['password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password'], 'string', 'min' => 6],
            [['confirmar_password'], 'compare', 'compareAttribute' => 'password'],
            [['email'], 'email'],
            [['email', 'username'], 'unique'],
            [['status'], 'default', 'value' => 10],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Usuário',
            'email' => 'E-mail',
            'password' => 'Senha',
            'confirmar_password' => 'Confirmar Senha',
            'password_hash' => 'Hash da Senha',
            'auth_key' => 'Chave de Autenticacao',
            'status' => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'created_by' => 'Criado por',
            'updated_by' => 'Atualizado por',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!empty($this->password)) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }

        if ($insert && empty($this->auth_key)) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        return true;
    }

    public function getCriadoPor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'created_by']);
    }

    public function getAtualizadoPor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'updated_by']);
    }

    public static function findIdentity($id): ?IdentityInterface
    {
        return static::find()
            ->andWhere(['id' => $id])
            ->one();
    }

    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return null;
    }

    public static function findByUsername(string $username): ?self
    {
        return static::find()
            ->andWhere(['username' => $username])
            ->one();
    }

    public function getId(): int|string
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): ?string
    {
        return (string)$this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
