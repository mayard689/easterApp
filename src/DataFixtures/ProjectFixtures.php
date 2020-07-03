<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Project;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    const PROJECTS=[
        'Easter App',
        'Art En Burger',
        'Tour Eiffel',
        'Versailles',
        'Wild Code School Odyssey',
        'Lab\'O',
    ];

    public function load(ObjectManager $manager)
    {
        $faker  =  Factory::create('fr_FR');
        $applications= $manager->getRepository(Application::class)->findAll();

        $projectCounter=0;
        foreach (self::PROJECTS as $projectName) {
            $project = new Project();
            $project->setName($projectName);
            $project->setDescription($faker->paragraph(3));
            $project->setDate(new DateTime($faker->date()));
//            $project->setQuotation()
            $project->setApplication($applications[array_rand($applications)]);

            $expert=rand(0, 100);
            $project->setExpert($expert);
            $junior=rand(0, 100-$expert);
            $project->setJunior($junior);
            $project->setConfirmed(100-$expert-$junior);

            $manager->persist($project);

            $this->addReference('project_'.$projectCounter, $project);
            $projectCounter++;
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
