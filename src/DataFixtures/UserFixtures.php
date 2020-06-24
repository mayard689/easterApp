<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i=0; $i<10; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail('user' . $i . '@easterapp.fr');
            $user->setRoles(['ROLE_APPUSER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'userpassword'
            ));
            $user->setCreationDate(new DateTime($faker->date()));

            $manager->persist($user);
        }

        $admin = new User();
        $admin->setEmail('johndoe@easterapp.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $admin->setFirstname('John');
        $admin->setLastname('Doe');
        $admin->setCreationDate(new DateTime($faker->date()));

        $manager->persist($admin);

        $manager->flush();
    }
}
