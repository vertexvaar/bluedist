<?php
/** @var \VerteXVaaR\BlueSprints\View\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'Hi dude!']);
?>

<h1>Hello <?= $name ?></h1>
<h2>Still don't have a name?</h2>
<p>
    Put it inside this form:
</p>
<form action="hello">
    <input type="text" name="name"/> <input type="submit" value="Send"/>
</form>
