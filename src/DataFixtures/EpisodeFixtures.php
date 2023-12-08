<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{   

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();


            for($i = 0; $i < 5000; $i++) {

                $episode = new Episode();
                $episode->setSeason($this->getReference('season_'.rand(0, 499)));
                $episodeTitle = $faker->sentence();
                $episode->setTitle($episodeTitle);
                $slug = $this->slugger->slug($episodeTitle);
                $episode->setSlug($slug);
                $episode->setSynopsis($faker->paragraph(3, true));
                $episode->setNumber($faker->numberBetween(1, 20));
                $episode->setDuration($faker->numberBetween(20, 60));
                
                $manager->persist($episode);
            
            }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}
