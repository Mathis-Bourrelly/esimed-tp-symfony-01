<?php

namespace App\Factory;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Advert>
 *
 * @method        Advert|Proxy create(array|callable $attributes = [])
 * @method static Advert|Proxy createOne(array $attributes = [])
 * @method static Advert|Proxy find(object|array|mixed $criteria)
 * @method static Advert|Proxy findOrCreate(array $attributes)
 * @method static Advert|Proxy first(string $sortedField = 'id')
 * @method static Advert|Proxy last(string $sortedField = 'id')
 * @method static Advert|Proxy random(array $attributes = [])
 * @method static Advert|Proxy randomOrCreate(array $attributes = [])
 * @method static AdvertRepository|RepositoryProxy repository()
 * @method static Advert[]|Proxy[] all()
 * @method static Advert[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Advert[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Advert[]|Proxy[] findBy(array $attributes)
 * @method static Advert[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Advert[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AdvertFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        $categories = CategoryFactory::repository()->findAll();
        return [
            'author' => self::faker()->name(),
            'category' => self::faker()->randomElement($categories),
            'content' => self::faker()->text(),
            'email' => self::faker()->email(),
            'price' => self::faker()->randomFloat(),
            'title' => self::faker()->text(40)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Advert $advert): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Advert::class;
    }
}
