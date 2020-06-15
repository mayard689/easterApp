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

    const MIN_STANDARD_FEATURE_PER_PROJECT=3;
    const DESCRIPTION_LENGTH=3;//number of sentences in the description
    const MAX_FEATURE_LOAD=12.5;

    public function load(ObjectManager $manager)
    {
        $this->associateStandardFeature($manager);
        $this->associateSpecificFeature($manager);

        $manager->flush();
    }

    private function associateSpecificFeature(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');

        $categoryCount=count(CategoryFixtures::CATEGORIES);
        $projectCount=count(ProjectFixtures::PROJECTS);
        $featureMaxIndex=FeatureFixtures::getProjectSpecificFeatureNumber();

        $featureCount=0;
        for ($projectIndex=0; $projectIndex<$projectCount; $projectIndex++) {
            $project=$this->getReference('project_'.$projectIndex);

            $featureNumber=rand(0, 2 * $featureMaxIndex / $projectCount);//attention aux -1
            for ($i=0; $i<$featureNumber; $i++) {
                $projectFeature = new ProjectFeature();
                $projectFeature->setProject($project);
                $projectFeature->setDescription($faker->paragraph(self::DESCRIPTION_LENGTH));
                $projectFeature->setDay(rand(0, 4 * self::MAX_FEATURE_LOAD)/4);

                $categoryIndex=rand(1, $categoryCount-1);
                $chosenCategory=$this->getReference('category_'.$categoryIndex);
                $projectFeature->setCategory($chosenCategory);

                if ($featureCount<=$featureMaxIndex) {
                    $projectFeature->setFeature($this->getReference('specific_feature_'.$featureCount));
                }


                $manager->persist($projectFeature);

                $this->addReference('specific_project_feature_'.$featureCount, $projectFeature);
                $featureCount++;
            }
        }
    }

    private function associateStandardFeature(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');

        $categoryMaxIndex=count(CategoryFixtures::CATEGORIES)-1;
        $projectMaxIndex=count(ProjectFixtures::PROJECTS)-1;
        $featureMaxIndex=count(FeatureFixtures::FEATURES)-1;


        $featureCount=0;
        for ($projectIndex=0; $projectIndex<=$projectMaxIndex; $projectIndex++) {
            $project=$this->getReference('project_'.$projectIndex);

            $featureIndexes=range(0, $featureMaxIndex);
            $featureNumber=rand(self::MIN_STANDARD_FEATURE_PER_PROJECT, $featureMaxIndex);

            for ($i=0; $i<$featureNumber; $i++) {
                $projectFeature = new ProjectFeature();
                $projectFeature->setProject($project);
                $projectFeature->setDescription($faker->paragraph(self::DESCRIPTION_LENGTH));
                $projectFeature->setDay(rand(0, 4 * self::MAX_FEATURE_LOAD)/4);

                $categoryIndex=rand(1, $categoryMaxIndex);
                $chosenCategory=$this->getReference('category_'.$categoryIndex);
                $projectFeature->setCategory($chosenCategory);

                $choosenFeature=array_rand($featureIndexes);
                $projectFeature->setFeature($this->getReference('feature_'.$featureIndexes[$choosenFeature]));
                unset($featureIndexes[$choosenFeature]);

                $manager->persist($projectFeature);

                $this->addReference('project_feature_'.$featureCount, $projectFeature);
                $featureCount++;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [ProjectFixtures::class, FeatureFixtures::class, CategoryFixtures::class];
    }
}
