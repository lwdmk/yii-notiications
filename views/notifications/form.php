<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\notifications\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\notifications\models\Notifications */
/* @var $form yii\widgets\ActiveForm */
/* @var array $receiver_fields_list */
/* @var array $list */
/* @var string $receiver_class */
/* @var string $receiver_values_method */
?>

<div class="notifications-form">

    <h1><?=($model->isNewRecord) ? 'Create' : 'Update';?> notification</h1>

    <p><?=Html::a('To list', ['index'], ['class' => 'btn btn-default'])?></p>

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->field($model, 'receiver_class')->hiddenInput(['value' => $receiver_class])->label(false);?>

    <?=$form->field($model, 'receiver_field')->dropDownList($receiver_fields_list, [
            'prompt'   => '-',
            'onchange' => '$.post(\'get-values\', {\'receiver_field\' : $(\'#notifications-receiver_field\').val()}, function(data) {
                $(\'#notifications-receiver_field_value_array\').empty().append(data);
            });'
        ]);?>

    <?=$form->field($model, 'receiver_field_value_array')
        ->dropDownList(('' === $receiver_values_method) ? [] : $receiver_class::$receiver_values_method(), [
            'multiple' => true,
        ]);?>

    <?=$form->field($model, 'title')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'subject')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'text')->textarea(['rows' => 6])?>

    <?=$form->field($model, 'methods_array')->dropDownList(Module::getInstance()->getTransportList(), [
        'multiple' => true
    ])?>

    <?php if (! empty($list)) { ?>
        <?=$form->field($model, 'owner_ids')->dropDownList($list, [
            'multiple' => true
        ])?>
    <?php } ?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? 'Create' : 'Update',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
