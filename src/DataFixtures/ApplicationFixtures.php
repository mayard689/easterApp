<?php

namespace App\DataFixtures;

use App\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture
{
    const APPLICATIONS=[
        'Application Web',
        'Application Mobile',
        'Application Plateforme'
    ];

    public function load(ObjectManager $manager)
    {
        $applicationCounter=0;
        foreach (self::APPLICATIONS as $applicationName) {
            $application = new Application();
            $application->setName($applicationName);

            $manager->persist($application);

            $this->addReference('application_'.$applicationCounter, $application);
            $applicationCounter++;
        }

        $manager->flush();
    }
}
