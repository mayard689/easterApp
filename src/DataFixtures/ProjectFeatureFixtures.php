<?php

namespace App\DataFixtures;

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

    private $faker;

    public function __construct()
    {
        $this->faker  = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $projectCount=count(ProjectFixtures::PROJECTS);
        $specificFeatureCount=FeatureFixtures::getProjectSpecificFeatureNumber();
        $standardFeatureCount=count(FeatureFixtures::FEATURES);

        $projectFeatures=[];
        for ($projectIndex=0; $projectIndex<$projectCount; $projectIndex++) {
            $project = $this->getReference('project_' . $projectIndex);

            //Associate specific Features
            $featureNumber=rand(0, 2 * $specificFeatureCount / $projectCount);
            for ($i=0; $i<$featureNumber; $i++) {
                $specificIndex=count($projectFeatures);
                if ($specificIndex<$specificFeatureCount) {
                    $projectFeatures[]=$this->makeSpecificFeature($project, $specificIndex);
                }
            }

            //Associate standard Features
            $featureIndexes=range(0, $standardFeatureCount-1);
            $featureNumber=rand(self::MIN_STANDARD_FEATURE_PER_PROJECT, $standardFeatureCount-1);
            for ($i=0; $i<$featureNumber; $i++) {
                $projectFeatures[]=$this->makeStandardFeature($project, $featureIndexes);
            }
        }

        foreach ($projectFeatures as $feature) {
            $manager->persist($feature);
        }

        $manager->flush();
    }

    private function makeSpecificFeature(Project $project, int $specificIndex) : ProjectFeature
    {
        $projectFeature=$this->makeGenericFeature($project);
        $projectFeature->setFeature($this->getReference('specific_feature_'.$specificIndex));
        return $projectFeature;
    }

    private function makeStandardFeature(Project $project, array &$featureIndexes) : ProjectFeature
    {
        $projectFeature=$this->makeGenericFeature($project);

        $chosenFeature=array_rand($featureIndexes);
        $projectFeature->setFeature($this->getReference('feature_'.$featureIndexes[$chosenFeature]));
        unset($featureIndexes[$chosenFeature]);

        return $projectFeature;
    }

    private function makeGenericFeature(Project $project) : ProjectFeature
    {
        $categoryCount=count(CategoryFixtures::CATEGORIES);

        $projectFeature = new ProjectFeature();

        $categoryIndex=rand(1, $categoryCount-1);
        $chosenCategory=$this->getReference('category_'.$categoryIndex);
        $projectFeature->setCategory($chosenCategory);

        $projectFeature->setProject($project);
        $projectFeature->setDescription($this->faker->paragraph(self::DESCRIPTION_LENGTH));
        $projectFeature->setDay(rand(0, 4 * self::MAX_FEATURE_LOAD)/4);

        $projectFeature->setIsHigh($this->faker->boolean());
        $projectFeature->setIsMiddle($this->faker->boolean());
        $projectFeature->setIsLow($this->faker->boolean());

        return $projectFeature;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [ProjectFixtures::class, FeatureFixtures::class, CategoryFixtures::class];
    }
}
