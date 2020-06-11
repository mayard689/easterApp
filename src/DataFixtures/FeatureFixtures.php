<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Feature;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class FeatureFixtures extends Fixture implements DependentFixtureInterface
{
    const FEATURES=[
        'Navbar',
        'Footer',
        'Authentification',
        'Panier',
        'Blog',
        'Forum',
        'Barre de recherche',
        'Article',
    ];

    public function load(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');
        $categories= $manager->getRepository(Category::class)->findAll();

        $featureCounter=0;
        foreach (self::FEATURES as $featureName) {
            $feature = new Feature();
            $feature->setName($featureName);
            $feature->setDescription($faker->paragraph(3));
            $feature->setDay(rand(0, 50)/4);
            $feature->setCategory($categories[array_rand($categories)]);
            $feature->setIsStandard(true);

            $manager->persist($feature);

            $this->addReference('feature_'.$featureCounter, $feature);
            $featureCounter++;
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
