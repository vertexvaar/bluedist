<?php
/** @var \VerteXVaaR\BlueSprints\Mvc\TemplateHelper $templateHelper */
$templateHelper->requireLayout('Basic/Html', ['pageTitle' => 'Hi dude!']);

/** @var \VerteXVaaR\BlueWelcome\Model\Fruit $fruit */
?>

<section>
    <h1>Change a <?= $fruit->getName() ?></h1>

    <p>
        Objects are target to many modifications, so updating a already persistend object is essential.<br/>
        Change the name or color of your selected fruit and save it, or go <a href="listFruits">back</a> to the list.
    </p>

    <form action="updateFruit" method="post">
        <input type="hidden" name="uuid" value="<?= $fruit->getUuid() ?>">

        <p>
            <label>
                Name
                <input type="text" name="name" value="<?= $fruit->getName() ?>"/>
            </label>
        </p>

        <p>
            <label>
                Color
                <input type="text" name="color" value="<?= $fruit->getColor() ?>"/>
            </label>
        </p>
        <input type="submit" value="Update it!">
    </form>

    <form action="deleteFruit" method="post">
        <input type="hidden" name="fruit" value="<?= $fruit->getUuid() ?>"/>
        <input type="submit" value="Or delete it :)">
    </form>


    <h2>The code behind this object update</h2>

    <pre>
        $fruit = Fruit::findByUuid($this->request->getArgument('uuid'));
        $fruit->setName($this->request->getArgument('name'));
        $fruit->setColor($this->request->getArgument('color'));
        $fruit->save();
        $this->redirect('listFruits');
    </pre>

    <h2>About save requests</h2>

    <p>
        HTTP requests are divided into two different types. Save and not save requests.
        A safe request may not initiate modify an object nor save it to peristence.
        Safe requests exist to retrieve information from a service or website without changing any data.
        To change the name of a fruit you must make a non save request, e.g. POST. PUT, POST and DELETE are considered
        not safe because they change objects and initiate their persistence.
    </p>

    <h2>Try it</h2>

    <p>
        <a href="updateFruit?name=failure&color=black&uuid=<?= $fruit->getUuid() ?>">This link</a> will save a fruit into the persistence but is of type GET.
           You will be rewarded with an exception.
    </p>
</section>
