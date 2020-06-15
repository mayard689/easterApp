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

    private static $projectFeatureNumber=0;

    public static function getProjectFeatureNumber()
    {
        return self::$projectFeatureNumber;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadStandardFeature($manager);
        $this->loadProjectSpecificFeature($manager);

        $manager->flush();
    }

    private function loadProjectSpecificFeature(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');

        $categoryMaxIndex=count(CategoryFixtures::CATEGORIES)-1;
        $projectNumber=count(ProjectFixtures::PROJECTS)-1;
        self::$projectFeatureNumber=5*$projectNumber;

        for ($i=0; $i<self::$projectFeatureNumber; $i++) {
            $feature = new Feature();
            $feature->setName("_".$faker->word);
            $feature->setDescription($faker->paragraph(3));
            $feature->setDay(rand(0, 50)/4);
            $feature->setIsStandard(false);

            $categoryIndex=rand(1, $categoryMaxIndex);
            $chosenCategory=$this->getReference('category_'.$categoryIndex);
            $feature->setCategory($chosenCategory);

            $manager->persist($feature);

            $this->addReference('specific_feature_'.$i, $feature);
        }
    }

    private function loadStandardFeature(ObjectManager $manager)
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
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [CategoryFixtures::class, ProjectFixtures::class];
    }
}
