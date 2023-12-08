<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Program;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();


            for($i = 0; $i < 50; $i++) {
                $program = new Program();
                $programTitle = $faker->sentence();
                $program->setTitle($programTitle);
                $slug = $this->slugger->slug($programTitle);
                $program->setSlug($slug);
                $program->setSynopsis($faker->paragraph(3, true));
                $program->setCategory($this->getReference('category_'. rand(0, 14)));
                $this->addReference('program_' .$i, $program);
                
                $manager->persist($program);
           
            }
        


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
