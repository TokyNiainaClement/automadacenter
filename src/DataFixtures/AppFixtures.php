<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
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
        // Vehicle
        for ($i = 0; $i < 5; $i++) { 
            $vehicle = new Vehicle();
            $vehicle->setTitle($this->faker->title())
            ->setPrice(mt_rand(10000000, 500000000))
            ->setBrand($this->faker->word())
            ->setModel($this->faker->word())
            ->setYear($this->faker->year())
            ->setMileage(mt_rand(10000, 100000))
            ->setFuelType($this->faker->word())
            ->setTransmission($this->faker->word())
            ->setColor($this->faker->colorName())
            ->setDescription($this->faker->text())
            ->setVehicleCondition($this->faker->word())
            ->setCity($this->faker->city());

            $manager-> persist($vehicle);
        }

        $manager->flush();
    }
}
