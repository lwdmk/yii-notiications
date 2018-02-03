<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\notifications\models\forms\ForceSendForm */
/* @var array $receiver_fields_list */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Force send';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-force-send">
    <h1><?=Html::encode($this->title)?></h1>

    <p>Please fill out the following fields to login:</p>

    <?php $form = ActiveForm::begin([
        'id'          => 'force-send-form',
        'options'     => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?=$form->field($model, 'receiver_field')->dropDownList($receiver_fields_list, [
        'prompt'   => '-',
        'onchange' => '$.post(\'get-values\', {\'receiver_field\' : $(\'#forcesendform-receiver_field\').val()}, function(data) {
                $(\'#forcesendform-receiver_field_values\').empty().append(data);
            });'
    ]);?>

    <?=$form->field($model, 'receiver_field_values')->dropDownList([], ['multiple' => true,]);?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?=Html::submitButton('Send', ['class' => 'btn btn-primary', 'name' => 'force-send-button'])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
