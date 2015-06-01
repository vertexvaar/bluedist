<?php
/** @var \VerteXVaaR\BlueSprints\Mvc\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'Hi dude!']);

/** @var \VerteXVaaR\BlueWelcome\Model\SubFolder\Tree $tree */
?>

<section>

    <h2>Put some Leaves on <?= $tree->getGenus() ?>'s branches!</h2>

    <?php
    foreach ($tree->getBranches() as $index => $branch) {
        ?>
        <p>
            Branch #<?= $index ?> with length: <?= $branch->getLength() ?>
            <br/>
            <?php
            foreach ($branch->getLeaves() as $leaf) {
                ?>
                Leaf #<?= $leaf->getNumber() ?>,
                <?php
            }
            ?>
        </p>
        <form action="addLeaf" method="post">
            <input type="hidden" value="<?= $tree->getUuid() ?>" name="tree">
            <input type="hidden" value="<?= $index ?>" name="branch">
            <input type="submit" value="Add a leaf">
        </form>
        <?php
    }
    ?>

</section>
