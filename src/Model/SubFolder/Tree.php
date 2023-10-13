<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Model\SubFolder;

use VerteXVaaR\BlueSprints\Mvc\Entity;

class Tree extends Entity
{
    protected ?Branch $mainBranch = null;

    /**
     * @var Branch[]
     */
    protected array $branches = [];

    protected string $genus = '';

    public function getMainBranch(): Branch
    {
        return $this->mainBranch;
    }

    public function setMainBranch(Branch $mainBranch)
    {
        $this->mainBranch = $mainBranch;
    }

    /**
     * @return Branch[]
     */
    public function getBranches(): array
    {
        return $this->branches;
    }

    /**
     * @param Branch[] $branches
     */
    public function setBranches(array $branches)
    {
        $this->branches = $branches;
    }

    public function getGenus(): string
    {
        return $this->genus;
    }

    public function setGenus(string $genus)
    {
        $this->genus = $genus;
    }
}
