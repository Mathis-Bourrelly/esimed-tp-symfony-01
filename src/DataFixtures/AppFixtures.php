<?php

namespace App\DataFixtures;

use App\Story\CreateAdvertStory;
use App\Story\CreateCategoryStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CreateCategoryStory::load();
        CreateAdvertStory::load();
    }
}
