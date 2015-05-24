<?php
/** @var \VerteXVaaR\BlueSprints\Mvc\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'Hi dude!']);
?>

<section>

    <h1>Some Fruits freshly picked from persistence</h1>

    <p>
        Hint: click on a fruit to edit it!
    </p>

    <ul>
        <?php
        /** @var \VerteXVaaR\BlueWelcome\Model\Fruit[] $fruits */
        foreach ($fruits as $fruit) {
            ?>
            <li>
                <a href="editFruit?fruit=<?= $fruit->getUuid() ?>">
                    I am a<?php
                    $name = $fruit->getName();
                    if (strlen($name) > 0) {
                        if (in_array(strtolower($name[0]), ['a', 'e', 'i', 'o', 'u', 'h'])) {
                            echo 'n';
                        }
                        echo ' ' . $name;
                    } else {
                        echo ' nameless fruit';
                    }
                    ?> and my color is <?= $fruit->getColor(); ?>
                </a></li>
            <?php
        }
        ?>
    </ul>

    <p>
        Not enough fruits? Just create your own fruit here:
    </p>

    <form action="createFruit" method="post">
        <p>
            <label>
                Name
                <input type="text" name="name"/>
            </label>
        </p>

        <p>
            <label>
                Color
                <input type="text" name="color"/>
            </label>
        </p>
        <input type="submit" value="Create it!">
    </form>

    <h2>The NoDB Storage</h2>

    <p>
        Since there is no configuration for any storage or database i owe you an explanation.
        BlueSprints just uses the file system. The full qualified class name of a model is converted into a directory
        structure. Inside the last directory all models are stored in files named by their automatically created uuid.
        The objects are serialized, since this is the fastest option to store all simple values without reflection,
        additional configuration or such.
    </p>

    <h2>WIP !</h2>

    <p>
        The Storage function is currently work in progress. Some missing features are automatic index tables for
        searching
        models by their properties or the deletion of those.
    </p>

    <h2>Like it? Or not?</h2>

    <p>
        Have a look at the code which creates a fruit from the form above:
    </p>

    <pre>
            $fruit = new Fruit();
            $fruit->setColor($this->request->getArgument('color'));
            $fruit->setName($this->request->getArgument('name'));
            $fruit->save();
    </pre>

    <p>
        As i promised you, it is easy as pie. If you want to alter the Model, say add a new property, just do it.
    </p>

    <p>
        <a href="./">Back to the index</a>
    </p>
</section>
