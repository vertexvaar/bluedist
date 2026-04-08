<?php

namespace VerteXVaaR\BlueDist\Seeder;

use Ramsey\Uuid\Uuid;
use VerteXVaaR\BlueDist\Model\Fruit;
use VerteXVaaR\BlueSeed\Seeder\Seeder;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;

readonly class FruitSeeder implements Seeder
{
    public const string IDENTIFIER = 'fruit';

    public function __construct(
        private Repository $repository,
    ) {}

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function seed(): void
    {
        if ($this->repository->countAll(Fruit::class) > 0) {
            return;
        }

        $data = [
            ['name' => 'Pineapple', 'color' => 'yellow'],
            ['name' => 'Orange', 'color' => 'orange'],
            ['name' => 'Kiwi', 'color' => 'brown and green'],
            ['name' => 'Watermelon', 'color' => 'Green and red'],
            ['name' => 'Strawberries', 'color' => 'Red'],
            ['name' => 'Lemon', 'color' => 'Yellow'],
            ['name' => 'Papaya', 'color' => 'Salmon'],
            ['name' => 'Fig', 'color' => 'Violet'],
        ];

        foreach ($data as $datum) {
            $fruit = new Fruit(Uuid::uuid4());
            $fruit->name = $datum['name'];
            $fruit->color = $datum['color'];
            $this->repository->persist($fruit);
        }
    }
}
