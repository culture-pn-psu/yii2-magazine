<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use culturePnPsu\magazine\models\Magazine;
use culturePnPsu\magazine\assets\AppAsset;

AppAsset::register($this);
$asset = AppAsset::register($this);



//echo $asset->baseUrl;

/* @var $this yii\web\View */
/* @var $model culturePnPsu\magazine\models\Magazine */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('magazine', 'Magazines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$path = Yii::$app->img->getUploadUrl('magazine/' . $model->id);
$this->registerCssFile($asset->baseUrl . '/css/modal-fullscreen.css');
?>
<div class='box box-info'>
    <div class='box-header'>
        <h3 class='box-title'><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu" role="menu">
                    <li> <?= Html::a(Yii::t('magazine', 'แก้ไข'), ['update', 'id' => $model->id], ['class' => '']) ?></li>
                    <li><?=
                        Html::a(Yii::t('magazine', 'ลบ'), ['delete', 'id' => $model->id], [
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('magazine', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ])
                        ?></li>
                </ul>
            </div>
        </div>

    </div><!--box-header -->

    <div class='box-body'>
        <div class='row'>
            <div class='col-sm-3'>
                <?php /* =Html::img(Yii::$app->img->getUploadUrl('magazine/'.$model->id).'1.jpg',['width'=>'100%']) */ ?>
            </div>
            <div class='col-sm-9'>

                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'id',
                        'title',
                        'detail:ntext',
                        //'magazine_type_id',
                        [
                            'attribute' => 'magazine_type_id',
                            'value' => $model->magazineType->title
                        ],
                        //'image_id',
                        [
                            'attribute' => 'status',
                            'format' => 'html',
                            'value' => $model->statusLabel
                        ],
                    ],
                ])
                ?>

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>



                <label class="control-label" for="image-name_file">
                    <?= ($model->image_id ? 'ไฟล์เดิม '  : 'ไฟล์ใหม่') ?>
                </label>

                <?=
                $form->field($model, 'image_id[]')->widget(FileInput::classname(), [
                    'options' => [
                        'accept' => 'image/*',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'initialPreview' => $model->initialPreview($model->image_id, 'docs', 'file'), 
                        'initialPreviewConfig' => $model->initialPreview($model->image_id, 'docs', 'config'), 
                        'uploadUrl' => Url::to(['uploadajax']),
                        'overwriteInitial'=>false,
                        'initialPreviewShowDelete'=>true,
                        'showPreview' => true,
                        'showRemove' => true,
                        'showUpload' => true,
                        //'initialPreview'=> $initialPreview,
                        //'initialPreviewConfig'=> $initialPreviewConfig,        
                        'uploadExtraData' => [
                            //'slide_id' => $model->id,
                            'id' => $model->id,
                            'upload_folder' => Magazine::UPLOAD_FOLDER . "/" . $model->id,
                        //'width' => ArtJob::width,
                        ],
                    //'maxFileSize' => 2000000,
                    //'maxFileCount' => 1,
                    ],
                ])->label(false)->hint('เป็นไฟล์ JPG เท่านั้น');
                ?>






                <?php ActiveForm::end(); ?> 
                <!--        <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('magazine', 'Create') : Yii::t('magazine', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>-->

                <?= Html::button('<i class="glyphicon glyphicon-camera"></i> แสดงเป็นหนังสือ', ['title' => Yii::t('app', 'แสดงเป็นหนังสือ'), 'class' => 'btn btn-default photo']); ?>
            </div>
        </div>
    </div><!--box-body pad-->
</div><!--box box-info-->


<?php
Modal::begin([
    'header' => Html::tag('h4', $model->title, ['class' => 'modal-title']),
    'id' => 'modal-fullscreen',
    'size' => Modal::SIZE_LARGE,
]);
echo Html::tag('div', '', ['id' => 'modalContent']);
?>

<?php Modal::end(); ?>


<?php
$this->registerJs(' 
      $("input[name=\'Image[name_file]\']").on("fileuploaderror", function(event, data, msg) {
    var form = data.form, files = data.files, extra = data.extra,
        response = data.response, reader = data.reader;
    console.log("File upload error");
   // get message
   alert(msg);
});
    
    $("input[name=\'Image[name_file]\']").on("fileuploaded", function(event, data, previewId, index) {
    //alert(55);
    var form = data.form, files = data.files, extra = data.extra,
        response = data.response.files, reader = data.reader;
    
        response = data.response.files
        console.log("form");
        console.log(form);
        console.log("files");
        console.log(files);
        console.log("extra");
        console.log(extra);
        console.log("response");
        console.log(response);
        console.log("reader");
        console.log(reader);
        //console.log("File batch upload complete"+files);
        if(response)
        loadImg(data.response.path,data.response.files);       
    });

var loadImg = function(path,id){
    //$("#magazine-image_id").val(id);
     $.get(
        "' . Url::to('set-file') . '",
        {
            id:"' . $model->id . '",
            image_id:id
        },
        function(data){
            conson.log(data);
        }
    );
}


');

##################################################
$this->registerJs("
//$('#modal-fullscreen').modal('show');
$('.photo').click(function(e) {            
    $.ajax({
        url: '" . Yii::$app->urlManager->createUrl('/magazine/default/preview') . "',
        data: {id:{$model->id}},
        type: 'GET',
        success: function(data) {     
            //$('#modal-fullscreen').find('#modalContent').html('');                   
            $('#modal-fullscreen').find('#modalContent').html(data);
            $('#modal-fullscreen').modal('show');
        }
    });      
}); 
    
");
