<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model culturePnPsu\magazine\models\Magazine */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('magazine', 'Magazines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class='box box-info'>
    <div class='box-header'>
     <h3 class='box-title'><?= Html::encode($this->title) ?></h3>


        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
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
    
    <div class='box-body pad'>

    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'title',
            'detail:ntext',
            'magazine_type_id',
            //'image_id',
            'status',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ],
    ]) ?>


    </div><!--box-body pad-->
 </div><!--box box-info-->
