<?php

namespace VerteXVaaR\BlueSeed\Seeder;

interface Seeder
{
    /**
     * @return string Unique name for this seeder.
     */
    public function getIdentifier(): string;

    /**
     * @return array<string> Array of names this seeder depends on
     */
    public function getDependencies(): array;

    /**
     * Do the seeding. If already seeded, the seeder should early return.
     *
     * @return void
     */
    public function seed(): void;
}
