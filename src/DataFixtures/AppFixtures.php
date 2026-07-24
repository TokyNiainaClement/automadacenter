<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        // User
        for ($i = 0; $i < 10; $i++) { 
            $user = new User();
            $user->setFullName($this->faker->firstNameMale() . " " . lcfirst($this->faker->lastName('male')))
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPlaintextPassword('password');

            $manager->persist($user);
        }
    
        $manager->flush();

    }
}
