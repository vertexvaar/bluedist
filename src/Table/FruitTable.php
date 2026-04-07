<?php

namespace VerteXVaaR\BlueDist\Table;

use VerteXVaaR\BlueAdmin\Table\BulkAction;
use VerteXVaaR\BlueAdmin\Table\Column;
use VerteXVaaR\BlueAdmin\Table\RowAction;
use VerteXVaaR\BlueAdmin\Table\Table;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueWeb\Enum\HttpMethod;

use function sprintf;

class FruitTable extends Table
{
    public function __construct()
    {
        $this
            ->addColumns(
                new Column('Name', fn(Fruit $fruit): string => $fruit->name),
                new Column('Color', fn(Fruit $fruit): string => $fruit->color),
            )
            ->addRowAction(
                new RowAction('Edit', fn(Fruit $fruit): string => sprintf('/admin/fruits/%s/edit', $fruit->identifier)),
            )
            ->addRowAction(
                new RowAction(
                    'Delete',
                    fn(Fruit $fruit): string => sprintf('/admin/fruits/%s/delete', $fruit->identifier),
                    HttpMethod::POST,
                ),
            )
            ->addBulkAction(new BulkAction('Delete Selected', '/admin/fruits/delete-multiple', HttpMethod::POST));
    }
}
