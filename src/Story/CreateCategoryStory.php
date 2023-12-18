<?php

namespace App\Story;

use App\Factory\AdvertFactory;
use Zenstruck\Foundry\Story;

final class CreateCategoryStory extends Story
{
    public function build(): void
    {
        AdvertFactory::createMany(5);
    }
}
