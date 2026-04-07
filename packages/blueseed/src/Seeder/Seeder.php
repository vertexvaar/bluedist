<?php

namespace VerteXVaaR\BlueSeed\Seeder;

interface Seeder
{
    /**
     * @return array<class-string<Seeder>>
     */
    public function getDependencies(): array;

    /**
     * Do the seeding. If already seeded, the seeder should early return.
     *
     * @return void
     */
    public function seed(): void;
}
