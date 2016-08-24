<?php

namespace culturePnPsu\magazine\models;

use Yii;

/**
 * This is the model class for table "magazine_type".
 *
 * @property integer $id
 * @property string $title
 *
 * @property Magazine[] $magazines
 */
class MagazineType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'magazine_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('magazine', 'ID'),
            'title' => Yii::t('magazine', 'ประเภท'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMagazines()
    {
        return $this->hasMany(Magazine::className(), ['magazine_type_id' => 'id']);
    }
    
    public static function getList()
    {
        return \yii\helpers\ArrayHelper::map(self::find()->all(),'id','title');
    }
}
