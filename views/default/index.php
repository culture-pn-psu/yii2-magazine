<?php

use yii\helpers\Html;
use yii\grid\GridView;
use culturePnPsu\magazine\models\Magazine;

/* @var $this yii\web\View */
/* @var $searchModel culturePnPsu\magazine\models\MagazineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('magazine', 'วารสาร');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class='box box-info'>
    <div class='box-header'>
     <!-- <h3 class='box-title'><?= Html::encode($this->title) ?></h3>-->
    </div><!--box-header -->

    <div class='box-body pad'>

        <p>
            <?= Html::a(Yii::t('magazine', 'เพิ่มวารสาร'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                //'id',
                'title',
                'detail:ntext',
                'magazine_type_id',
                //'image_id',
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'filter' => Magazine::getItemStatus(),
                    'value' => 'statusLabel'
                ],
                // 'created_by',
                // 'created_at',
                // 'updated_by',
                // 'updated_at',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>


    </div><!--box-body pad-->
</div><!--box box-info-->
