<?php


namespace App\DataFixtures;

use App\Entity\Quotation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuotationFixtures extends Fixture
{
    const QUOTATIONS = [
        "high",
        "middle",
        "low",
    ];

    public function load(ObjectManager $manager)
    {
        $count = 0;
        foreach (self::QUOTATIONS as $quot) {
            $quotation = new Quotation();
            $quotation->setName($quot);
            $manager->persist($quotation);
            $this->addReference('quotation_' . $count, $quotation);
            $count++;
        }
        $manager->flush();
    }
}
