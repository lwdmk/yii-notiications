<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\notifications\models\search\SearchNotifications */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-index">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?=Html::a('Create Notifications', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'text:ntext',
            'methods',
            'created_at',

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}{force}',
                'buttons'  => [
                    'force' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-send"></i>',['notifications/force-send', 'id' => $model->id]);
                    }
                ]
            ],
        ],
    ]);?>
</div>
