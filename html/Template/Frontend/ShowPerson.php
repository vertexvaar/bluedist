<?php
/** @var \VerteXVaaR\BlueSprints\View\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'list of persons']);


/** @var \VerteXVaaR\BlueSprints\Model\Person $person */
?>
<h1>
    <?= $person->getFullName() ?>
</h1>
<div>
    <table>
        <tr>
            <td>
                UUID
            </td>
            <td>
                <?= $person->getUuid() ?>
            </td>
        </tr>
        <tr>
            <td>
                Created at
            </td>
            <td>
                <?= $person->getCreationTime()->format('d.m.Y H:i:s') ?>
            </td>
        </tr>
    </table>
</div>
