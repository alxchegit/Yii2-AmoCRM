<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Создать сделку';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-create">
    <h1><?= Html::encode($this->title) ?></h1>
        <p>
            Заполните поля
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'create-form']); ?>

                    <?= $form->field($model, 'leadName')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'notes')->textarea(['rows' => 2]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

</div>
