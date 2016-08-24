<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;
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


    </div><!--box-body pad-->
</div><!--box box-info-->



<div id="canvas">

    <div class="zoom-icon zoom-icon-in"></div>

    <div class="magazine-viewport">
        <div class="container">
            <div class="magazine">
                <!-- Next button -->
                <div ignore="1" class="next-button"></div>
                <!-- Previous button -->
                <div ignore="1" class="previous-button"></div>
            </div>
        </div>
    </div>

    <!-- Thumbnails -->
    <div class="thumbnails">
        <div>
            <ul>
                <?php for ($i = 0; $i < $noOfPagesInPDF; $i++): ?>

                    <?php if ($i == 0): ?>

                        <li class="i">
                            <img src="<?= $path . $i ?>.jpg" width="100%" class="page-<?= $i ?>">
                            <span><?= $i ?></span>
                        </li>
                     <?php elseif ($i > 0 && $i < $noOfPagesInPDF) : ?>    
                        
                            <?php if ($i%2 == 0): ?>
                            <li class="d">
                            <img src="<?= $path . $i ?>.jpg" width="100%" class="page-<?= $i ?>">
                            <span><?= $i ?></span>
                             <?php elseif ($i%2 == 1) : ?>    
                            <img src="<?= $path . $i ?>.jpg" width="100%"class="page-<?= $i ?>">
                            <span><?= $i ?></span>
                            
                            </li>
                            <?php endif; ?>
                         <?php elseif ($i == $noOfPagesInPDF) : ?>
                        <li class="i">
                           <img src="<?= $path . $i ?>.jpg" width="100%"  class="page-<?= $i ?>">
                            <span><?= $i ?></span>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                <ul>
                    <div>	
                    </div>
                    </div>








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

 	$('#canvas').fadeIn(1000);

 	var flipbook = $('.magazine');

 	// Check if the CSS was already loaded
	
	if (flipbook.width()==0 || flipbook.height()==0) {
		setTimeout(loadApp, 10);
		return;
	}
	
	// Create the flipbook

	flipbook.turn({
			
			// Magazine width

			width: 922,

			// Magazine height

			height: 600,

			// Duration in millisecond

			duration: 1000,

			// Hardware acceleration

			acceleration: !isChrome(),

			// Enables gradients

			gradients: true,
			
			// Auto center this flipbook

			autoCenter: true,

			// Elevation from the edge of the flipbook when turning a page

			elevation: 50,

			// The number of pages

			pages: $noOfPagesInPDF,

			// Events

			when: {
				turning: function(event, page, view) {
					
					var book = $(this),
                                        currentPage = book.turn('{$path}'),
					pages = book.turn('{$path}');
			
					// Update the current URI

					Hash.go('".$path."/' + page).update();

					// Show and hide navigation buttons

					disableControls(page);
					

					$('.thumbnails .page-'+currentPage).
						parent().
						removeClass('current');

					$('.thumbnails .page-'+page).
						parent().
						addClass('current');



				},

				turned: function(event, page, view) {

					disableControls(page);

					$(this).turn('center');

					if (page==1) { 
						$(this).turn('peel', 'br');
					}

				},

				missing: function (event, pages) {

					// Add pages that aren't in the magazine

					for (var i = 0; i < pages.length; i++)
						addPage(pages[i], $(this));

				}
			}

	});

	// Zoom.js

	$('.magazine-viewport').zoom({
		flipbook: $('.magazine'),

		max: function() { 
			
			return largeMagazineWidth()/$('.magazine').width();

		}, 

		when: {

			swipeLeft: function() {

				$(this).zoom('flipbook').turn('next');

			},

			swipeRight: function() {
				
				$(this).zoom('flipbook').turn('previous');

			},

			resize: function(event, scale, page, pageElement) {

				if (scale==1)
					loadSmallPage(page, pageElement);
				else
					loadLargePage(page, pageElement);

			},

			zoomIn: function () {

				$('.thumbnails').hide();
				$('.made').hide();
				$('.magazine').removeClass('animated').addClass('zoom-in');
				$('.zoom-icon').removeClass('zoom-icon-in').addClass('zoom-icon-out');
				
				if (!window.escTip && !$.isTouch) {
					escTip = true;

					$('<div />', {'class': 'exit-message'}).
						html('<div>Press ESC to exit</div>').
							appendTo($('body')).
							delay(2000).
							animate({opacity:0}, 500, function() {
								$(this).remove();
							});
				}
			},

			zoomOut: function () {

				$('.exit-message').hide();
				$('.thumbnails').fadeIn();
				$('.made').fadeIn();
				$('.zoom-icon').removeClass('zoom-icon-out').addClass('zoom-icon-in');

				setTimeout(function(){
					$('.magazine').addClass('animated').removeClass('zoom-in');
					resizeViewport();
				}, 0);

			}
		}
	});

	// Zoom event

	if ($.isTouch)
		$('.magazine-viewport').bind('zoom.doubleTap', zoomTo);
	else
		$('.magazine-viewport').bind('zoom.tap', zoomTo);


	// Using arrow keys to turn the page

	$(document).keydown(function(e){

		var previous = 37, next = 39, esc = 27;

		switch (e.keyCode) {
			case previous:

				// left arrow
				$('.magazine').turn('previous');
				e.preventDefault();

			break;
			case next:

				//right arrow
				$('.magazine').turn('next');
				e.preventDefault();

			break;
			case esc:
				
				$('.magazine-viewport').zoom('zoomOut');	
				e.preventDefault();

			break;
		}
	});

	

	$(window).resize(function() {
		resizeViewport();
	}).bind('orientationchange', function() {
		resizeViewport();
	});

	

	resizeViewport();

	$('.magazine').addClass('animated');

}

// Zoom icon

 $('.zoom-icon').bind('mouseover', function() { 
 	
 	if ($(this).hasClass('zoom-icon-in'))
 		$(this).addClass('zoom-icon-in-hover');

 	if ($(this).hasClass('zoom-icon-out'))
 		$(this).addClass('zoom-icon-out-hover');
 
 }).bind('mouseout', function() { 
 	
 	 if ($(this).hasClass('zoom-icon-in'))
 		$(this).removeClass('zoom-icon-in-hover');
 	
 	if ($(this).hasClass('zoom-icon-out'))
 		$(this).removeClass('zoom-icon-out-hover');

 }).bind('click', function() {

 	if ($(this).hasClass('zoom-icon-in'))
 		$('.magazine-viewport').zoom('zoomIn');
 	else if ($(this).hasClass('zoom-icon-out'))	
		$('.magazine-viewport').zoom('zoomOut');

 });

 $('#canvas').hide();


// Load the HTML4 version if there's not CSS transform

yepnope({
	test : Modernizr.csstransforms,
	yep: ['".$asset->baseUrl."/js/lib/turn.js'],
	nope: ['".$asset->baseUrl."/js/lib/turn.html4.min.js'],
	both: ['".$asset->baseUrl."/js/lib/zoom.min.js', '".$asset->baseUrl."/js/magazine.js', '".$asset->baseUrl."/css/magazine.css'],
	complete: loadApp
});
");
                    