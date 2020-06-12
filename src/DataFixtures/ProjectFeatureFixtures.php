<?php

namespace App\DataFixtures;

use App\Entity\Feature;
use App\Entity\Project;
use App\Entity\ProjectFeature;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProjectFeatureFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');
        //$features= $manager->getRepository(Feature::class)->findAll();

        $categoryMaxIndex=count(CategoryFixtures::CATEGORIES)-1;
        $projectMaxIndex=count(ProjectFixtures::PROJECTS)-1;
        $featureMaxIndex=count(FeatureFixtures::FEATURES)-1;
        $featuresTabIndex=range(0, $featureMaxIndex);

        $featureCount=0;
        for ($projectIndex=0; $projectIndex<=$projectMaxIndex; $projectIndex++) {
            $project=$this->getReference('project_'.$projectIndex);

            $featureNumber=rand(3, $featureMaxIndex);
            for ($i=0; $i<$featureNumber; $i++) {
                $projectFeature = new ProjectFeature();
                $projectFeature->setProject($project);
                $projectFeature->setDescription($faker->paragraph(3));
                $projectFeature->setDay(rand(0, 50)/4);

                $categoryIndex=rand(1, $categoryMaxIndex);
                $chosenCategory=$this->getReference('category_'.$categoryIndex);
                $projectFeature->setCategory($chosenCategory);

                $featureIndex=array_rand($featuresTabIndex);
                $projectFeature->setFeature($this->getReference('feature_'.$featuresTabIndex[$featureIndex]));

                $manager->persist($projectFeature);

                $this->addReference('project_feature_'.$featureCount, $projectFeature);
                $featureCount++;
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [ProjectFixtures::class, FeatureFixtures::class, CategoryFixtures::class];
    }
}
