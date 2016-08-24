<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use culturePnPsu\magazine\models\Magazine;
use culturePnPsu\magazine\models\MagazineType;

/* @var $this yii\web\View */
/* @var $model culturePnPsu\magazine\models\Magazine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-sm-3">
        
        
    </div>
    <div class="col-sm-9">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'magazine_type_id')->dropDownList(MagazineType::getList(), ['prompt' => 'เลือก']) ?>

        <?= $form->field($model, 'image_id')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'status')->dropDownList(Magazine::getItemStatus(), ['prompt' => 'เลือก']) ?>



        <div class="form-group">
<?= Html::submitButton($model->isNewRecord ? Yii::t('magazine', 'Create') : Yii::t('magazine', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

<?php ActiveForm::end(); ?>

    </div>
</div>
