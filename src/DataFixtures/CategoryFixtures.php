<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture
{
    const CATEGORIES=[
        'Navigation',
        'Structure',
        'Administration',
        'Visiteur',
    ];

    public function load(ObjectManager $manager)
    {
        $categoryCounter=0;
        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);

            $manager->persist($category);

            $this->addReference('category_'.$categoryCounter, $category);
            $categoryCounter++;
        }

        $manager->flush();
    }
}
