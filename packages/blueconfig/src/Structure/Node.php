<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueConfig\Structure;

interface Node
{
    public function getKey(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getType(): string;

    public function getDefault(): mixed;
}
