<?php

/* @var $this yii\web\View */
/** @var $leads Lead Service */
/** @var $users Users Service */
/** @var $contacts Contacts Service */
/** @var $companies Companies Service */

use yii\helpers\Html;

$this->title = 'Список сделок';

$amo = Yii::$app->params['amocrm'];
$baseDomain = $amo['baseDomain'];
?>
<div class="site-index">
    <div class="body-content container">
            <div class="row ">
                <h2 class="align-content-center"><?= $this->title ?></h2>
                <p>Интеграция для <strong><?= $baseDomain ?></strong></p>
                <p class="col-lg-3">
                    <?= Html::a('Новая сделка', ['create'], ['class' => 'btn btn-lg btn-success']) ?>
                </p>
            </div>

        <div class="row">
        <?php foreach ($leads as $key => $lead) { ?>
            <div class="amocrm-lead alert alert-info">
                <div class="hidden"> <?= $lead->id ?> </div>
                <div class="lead-title">
                    <a data-toggle="collapse" data-target="#lead-<?= $key ?>" >
                        <h2 > <?= $lead->name ?> </h2>
                    </a>
                    <ul class="lead-title_buttons">
                        <li class="delete"><?= Html::a('Удалить', ['#']) ?></li>
                    </ul>
                </div>

                <p>отв: <strong>
                    <?= $users->getOne($lead->responsibleUserId)->name ?>
                </strong></p>
                <div class="amocrm-lead_body collapse" id="lead-<?= $key ?>">
                <p>Контакты:</p>
                <ul>
                    <?php if ($lead->contacts) :?>
                <?php foreach ($lead->contacts as $contact) : ?>
                <?php $cont = $contacts->getOne($contact->id) ?>
                    <li> <?= $cont->name ?>
                        <ul>
                            <?php $contactFields = $cont->getCustomFieldsValues() ?>
                            <?php foreach ($contactFields as $field) { ?>
                                <li>
                                    <?= $field->fieldName ?>
                                    <ul>
                                        <?php foreach ($field->getValues()->toArray() as $fieldArray) { ?>
                                           <li>
                                                <?= $fieldArray['value'] ?>
                                           </li>
                                        <?php } ?>

                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
                <?php endif; ?>
                </ul>
                    <?php if ($compId = $lead->company->id) : ?>
                        <?php $company = $companies->getOne($compId) ?>
                        <p>Компания: <strong><?= $company->name ?></strong></p>
                        <ul>
                            <?php $contactFields = $company->getCustomFieldsValues() ?>
                            <?php foreach ($contactFields as $field) { ?>
                                <li>
                                    <?= $field->fieldName ?>
                                    <ul>
                                        <?php foreach ($field->getValues()->toArray() as $fieldArray) { ?>
                                            <li>
                                                <?= $fieldArray['value'] ?>
                                            </li>
                                        <?php } ?>

                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                <?php endif; ?>
                </div>
               
            </div>
        <?php } ?>

        </div>
    </div>
</div>
