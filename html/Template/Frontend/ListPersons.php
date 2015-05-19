<?php
/** @var \VerteXVaaR\BlueSprints\View\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'list of persons']);
?>

<ul><?php
	/** @var \VerteXVaaR\BlueSprints\Model\Person[] $persons */
	foreach ($persons as $person) { ?>
		<li>
			<a href="showPerson?person=<?= $person->getUuid(); ?>"><?= $person->getFullName() ?></a>
		</li>
	<?php } ?>
</ul>
