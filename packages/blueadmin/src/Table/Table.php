<?php

namespace VerteXVaaR\BlueAdmin\Table;

use Closure;

class Table
{
    /** @var array<Column> */
    protected(set) array $columns = [];
    /** @var array<RowAction> */
    protected(set) array $rowActions = [];
    /** @var array<BulkAction> */
    protected(set) array $bulkActions = [];
    protected(set) Closure $query;

    public function addColumns(Column ...$columns): static
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        return $this;
    }

    public function addColumn(Column $column): static
    {
        $this->columns[] = $column;
        return $this;
    }

    public function addRowAction(RowAction $rowAction): static
    {
        $this->rowActions[] = $rowAction;
        return $this;
    }

    public function addBulkAction(BulkAction $bulkAction): static
    {
        $this->bulkActions[] = $bulkAction;
        return $this;
    }
}
