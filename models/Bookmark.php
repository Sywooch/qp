<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bookmark".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 *
 * @property Good\Good $product
 * @property User $user
 */
class Bookmark extends CachedActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bookmark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id'], 'required', 'message' => 'Необходимо авторизоваться.'],
            [['user_id', 'product_id'], 'integer'],
            [['user_id', 'product_id'], 'unique', 'targetAttribute' => ['user_id', 'product_id'],
                'message' => 'The combination of User ID and Product ID has already been taken.'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Good\Good::className(),
                'targetAttribute' => ['product_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Good\Good::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
