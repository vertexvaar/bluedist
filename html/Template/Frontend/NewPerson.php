<?php
/** @var \VerteXVaaR\BlueSprints\View\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'list of persons']);
?>

<form action="createPerson" method="post">
    <label>
        First Name <input type="text" name="firstName">
    </label>
    <label>
        Last Name <input type="text" name="lastName"/>
    </label>
    <input type="submit" value="Create">
</form>
