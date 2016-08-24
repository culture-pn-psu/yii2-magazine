<?php

namespace culturePnPsu\magazine\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "magazine".
 *
 * @property integer $id
 * @property string $title
 * @property string $detail
 * @property integer $magazine_type_id
 * @property string $image_id
 * @property integer $status
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_by
 * @property integer $updated_at
 *
 * @property MagazineType $magazineType
 */
class Magazine extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'magazine';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'magazine_type_id'], 'required'],
            [['detail', 'image_id'], 'string'],
            [['magazine_type_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
                //[['image_id'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('magazine', 'ID'),
            'title' => Yii::t('magazine', 'ชื่อ'),
            'detail' => Yii::t('magazine', 'รายละเอียด'),
            'magazine_type_id' => Yii::t('magazine', 'ประเภท'),
            'image_id' => Yii::t('magazine', 'ไฟล์'),
            'status' => Yii::t('magazine', 'สถานะ'),
            'created_by' => Yii::t('magazine', 'สร้างโดย'),
            'created_at' => Yii::t('magazine', 'สร้างเมื่อ'),
            'updated_by' => Yii::t('magazine', 'แก้ไขโดย'),
            'updated_at' => Yii::t('magazine', 'แก้ไขเมื่อ'),
        ];
    }

    public static function itemsAlias($key) {
        $items = [
            'status' => [
                0 => Yii::t('app', 'ร่าง'),
                1 => Yii::t('app', 'แสดง'),
                2 => Yii::t('app', 'ซ่อน'),
            ],
        ];
        return ArrayHelper::getValue($items, $key, []);
    }

    public function getStatusLabel() {
        $status = ArrayHelper::getValue($this->getItemStatus(), $this->status);
        $status = ($this->status === NULL) ? ArrayHelper::getValue($this->getItemStatus(), 0) : $status;
        switch ($this->status) {
            case '0' :
            case NULL :
                $str = '<span class="label label-danger">' . $status . '</span>';
                break;
            case '1' :
                $str = '<span class="label label-success">' . $status . '</span>';
                break;
            case '2' :
                $str = '<span class="label label-warning">' . $status . '</span>';
                break;
            default :
                $str = $status;
                break;
        }

        return $str;
    }

    public static function getItemStatus() {
        return self::itemsAlias('status');
    }

    ####################################
    ####################################

    const UPLOAD_FOLDER = 'magazine';

    //const width = 1024;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMagazineType() {
        return $this->hasOne(MagazineType::className(), ['id' => 'magazine_type_id']);
    }

    public function initialPreview($data, $field, $type = 'file') {
        $initial = [];
        $files = '';
        if ($data != NULL) {
            $files = \yii\helpers\Json::decode($data);
            ksort($files);
        }
        //$files = '';
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                if ($type == 'file') {
                    $initial[] = \yii\helpers\Html::img(Yii::$app->img->getUploadUrl(self::UPLOAD_FOLDER . '/' . $this->id) . $value, ['class' => 'file-preview-image']);
                } elseif ($type == 'config') {
                    $initial[] = [
                        'caption' => $value,
                        'width' => '120px',
                        'url' => \yii\helpers\Url::to(['deletefile-ajax', 'id' => $this->id, 'fileName' => $value, 'field' => $field, 'folder' => self::UPLOAD_FOLDER]),
                        'key' => $value
                    ];
                } else {
                    $initial[] = Html::img(self::getUploadUrl() . $this->ref . '/' . $value, ['class' => 'file-preview-image', 'alt' => $model->file_name, 'title' => $model->file_name]);
                }
            }
        }
        return $initial;
    }

    public static function findFiles($pathFile) {
        $files = [];
        $findFiles = \yii\helpers\FileHelper::findFiles($pathFile);
        ksort($findFiles);
        // set pdfs as target folder
        //print_r($findFiles);
        foreach ($findFiles as $index => $file) {
            if (strpos($file, 'thumbnail') === false) {
                $nameFicheiro = substr($file, strrpos($file, '/') + 1);
                $files[$nameFicheiro] = $nameFicheiro;
            }
        }
        return $files ? \yii\helpers\Json::encode($files) : null;
    }
    
    public function getImgTemp(){               
        $img = Yii::$app->img;
        $imgIndex = '1.jpg';
        return ($img->chkImg($this->thisPath,$imgIndex)?$img->getUploadUrl($this->thisPath).$imgIndex:$img->getUploadUrl().$img->no_img);
    }
    
    public function getThisPath(){               
        return self::UPLOAD_FOLDER.'/'.$this->id;
    }
    
    public static function listIndex(){
        $model = self::find()->all();
        return $model;        
    }

}
