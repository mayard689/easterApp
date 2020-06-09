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
        $projects= $manager->getRepository(Project::class)->findAll();
        $features= $manager->getRepository(Feature::class)->findAll();

        $featureCount=0;
        foreach ($projects as $project) {
            $availableFeatures=$features;

            $featureNumber=rand(1, count($availableFeatures));
            for ($i=0; $i<$featureNumber; $i++) {
                $projectFeature = new ProjectFeature();
                $projectFeature->setProject($project);
                $featureIndex=array_rand($availableFeatures);
                $projectFeature->setFeature($features[$featureIndex]);
                $projectFeature->setDescription($faker->paragraph(3));
                $projectFeature->setDay(rand(0, 50)/4);

                unset($availableFeatures[$featureIndex]);

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
        return [ProjectFixtures::class, FeatureFixtures::class];
    }
}
