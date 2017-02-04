<?php
/** @var \VerteXVaaR\BlueSprints\Mvc\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'Hi dude!']);

/** @var \VerteXVaaR\BlueWelcome\Model\SubFolder\Tree $tree */
/** @var int $numberOfBranches */
?>

<section>

	<h2>Grow some branches on <?= $tree->getGenus() ?>!</h2>

	<form action="growBranches" method="post">
		<input type="hidden" value="<?= $tree->getUuid() ?>" name="tree">
        <?php
        for ($i = 0; $i < $numberOfBranches; $i++) {
            ?>
			<p>
				<label>
					Branch <?= $i ?> lentgh
					<input type="text" name="branches[<?= $i ?>][length]">
				</label>
			</p>
            <?php
        }
        ?>
		<input type="submit" value="Grow branches">
	</form>
</section>
