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
                'image_id',
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => $model->statusLabel
                ],
            ],
        ])
        ?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>



        <?=
        $form->field(new \backend\modules\image\models\Image, 'name_file')->widget(FileInput::classname(), [
            //'options' => ['accept' => 'pdf'],
            'pluginOptions' => [
                'uploadUrl' => Url::to(['/file/default/uploadfileajax']),
                //'overwriteInitial'=>false,
                'initialPreviewShowDelete' => true,
                'showPreview' => false,
                'showRemove' => true,
                'showUpload' => true,
                'allowedFileExtensions' => ['pdf'],
                //'initialPreview'=> $initialPreview,
                //'initialPreviewConfig'=> $initialPreviewConfig,        
                'uploadExtraData' => [
                    //'slide_id' => $model->id,
                    'upload_folder' => Magazine::UPLOAD_FOLDER . "/" . $model->id,
                //'width' => ArtJob::width,
                ],
            //'maxFileCount' => 1,
            ],
                // 'options' => ['accept' => 'pdf', 'id' => 'name_file']
        ])->label('ไฟล์');
        ?>


        <div class="form-group">
<?= Html::submitButton($model->isNewRecord ? Yii::t('magazine', 'Create') : Yii::t('magazine', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        
        

<?php ActiveForm::end(); ?>

        <?= Html::button('<i class="glyphicon glyphicon-camera"></i> โหลดรูป', ['title' => Yii::t('app', 'โหลดรูป'), 'class' => 'btn btn-default photo']);?>

    </div><!--box-body pad-->
</div><!--box box-info-->


<?php Modal::begin([
    'id' => 'modal-fullscreen',
    'size'=> Modal::SIZE_LARGE,    
    ]); ?>
<div class="row">
    <div class="col-sm-12">
<!--        <div class="flipbook-viewport">
            <div class="container">-->
                <div class="flipbook">
                    <?php for ($i = 0; $i < $noOfPagesInPDF; $i++): ?>
                        <div style="background-image:url(<?= $path . $i ?>.jpg);background-size:<?=$width?>px <?=$height?>px;"></div>
<?php endfor; ?>
                </div>
<!--            </div>
        </div>-->
    </div>
</div>
<?php Modal::end(); ?>


<?php
$this->registerJs(' 
      
    
    $("input[name=\'Image[name_file]\']").on("fileuploaded", function(event, data, previewId, index) {
    //alert(55);
    var form = data.form, files = data.files, extra = data.extra,
        response = data.response.files, reader = data.reader;
    
        response = data.response.files
        console.log("1"+form+"2"+files+"3"+extra+"4"+response+"5"+reader);
        console.log("File batch upload complete"+files);
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
                        
function loadApp() {
        $('.flipbook').turn({
            // Width
            width:{$width}*2,			
            // Height
            height:{$height},
            // Elevation
            elevation: 50,			
            // Enable gradients
            gradients: true,			
            // Auto center this flipbook
            autoCenter: true
	});
        $(window).resize(function() {
		resizeViewport();
	}).bind('orientationchange', function() {
		resizeViewport();
	});
}

// Load the HTML4 version if there's not CSS transform

yepnope({
	test : Modernizr.csstransforms,
	yep: ['" . $asset->baseUrl . "/js/lib/turn.js'],
	nope: ['" . $asset->baseUrl . "/js/lib/turn.html4.min.js'],
	both: [
                //'" . $asset->baseUrl . "/js/lib/zoom.min.js',
                //'" . $asset->baseUrl . "/js/magazine.js',
                '" . $asset->baseUrl . "/css/basic.css'
               ],
	complete: loadApp
});

$('#modal-fullscreen').modal('show');
$('.photo').click(function(e) {            
        $('#modal-fullscreen').modal('show');        
    }); 
");
