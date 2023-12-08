<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create();

        for($i = 0; $i < 15; $i++) {
            $category = new Category();
            $category->setName($faker->sentence());
            $this->addReference('category_' . $i, $category);
            $manager->persist($category);
        }

        $manager->flush();
    }
}