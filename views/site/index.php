<?php

/* @var $this yii\web\View */
/** @var $leads Leads collections */
/** @var $user Users Service */
/** @var $contacts Contacts Service */
/** @var $companies Companies Service */

$this->title = 'My Yii Application';

?>
<div hidden>
    <pre>
        <?php print_r($leads) ?>
    </pre>
</div>
<div class="site-index">
    <div class="body-content">
        <div class="row">

        <?php foreach ($leads as $key => $lead) { ?>
            <div class="amocrm-lead alert alert-info">
                <div class="hidden"> <?= $lead->id ?> </div>
               <a data-toggle="collapse" data-target="#lead-<?= $key ?>"><h2 > <?= $lead->name ?> </h2></a>
               <p>отв: <strong>
                    <?= $users->getOne($lead->responsibleUserId)->name ?> 
                </strong></p>
                <div class="amocrm-lead_body collapse" id="lead-<?= $key ?>">
                <p>Контакты:</p>
                <ul> 
                <?php foreach ($lead->contacts as $key => $contact) { ?>
                <?php $cont = $contacts->getOne($contact->id) ?>
                    <li> <?= $cont->name ?>
                        <ul>
                            <?php $contactFields = $cont->getCustomFieldsValues() ?>
                            <?php foreach ($contactFields as $key => $field) { ?>
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
                <?php } ?>
                </ul>
                <?php $company = $companies->getOne($lead->company->id) ?>
                <p>Компания: <strong><?= $company->name ?></strong></p>
            </div>
               
            </div>
        <?php } ?>

        </div>
    </div>
</div>
