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

    const MEAN_SPECIFIC_FEATURE_PER_PROJECT=3;
    const DESCRIPTION_LENGTH=3;//number of sentences in the description
    const MAX_FEATURE_LOAD=12.5;

    public static function getProjectSpecificFeatureNumber() : int
    {
        return self::MEAN_SPECIFIC_FEATURE_PER_PROJECT * count(ProjectFixtures::PROJECTS);
    }

    public function load(ObjectManager $manager)
    {
        $this->loadStandardFeature($manager);

        $manager->flush();
    }

    private function loadStandardFeature(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');
        $categoryMaxIndex=count(CategoryFixtures::CATEGORIES)-1;

        $featureCounter=0;
        foreach (self::FEATURES as $featureName) {
            $feature = new Feature();
            $feature->setName($featureName);
            $feature->setDescription($faker->paragraph(self::DESCRIPTION_LENGTH));
            $feature->setDay(rand(0, 4 * self::MAX_FEATURE_LOAD)/4);
            $feature->setIsStandard(true);

            $categoryIndex=rand(1, $categoryMaxIndex);
            $chosenCategory=$this->getReference('category_'.$categoryIndex);
            $feature->setCategory($chosenCategory);

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
