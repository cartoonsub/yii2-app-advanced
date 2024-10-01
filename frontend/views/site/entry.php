<?php
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'message') ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php
        VarDumper::dump($countries, 10, true);
    ?>
<?php ActiveForm::end(); ?>
