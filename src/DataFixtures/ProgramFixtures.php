<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $program = new Program();
        $program->setTitle('Walking dead');
        $program->setSynopsis('Des zombies envahissent la terre');
        $program->setCategory($this->getReference('category_Action'));
        $manager->persist($program);
        $program = new Program();
        $program->setTitle('House of Cards');
        $program->setSynopsis('Politique');
        $program->setCategory($this->getReference('category_Aventure'));
        $manager->persist($program);
        $program = new Program();
        $program->setTitle('Moi Moche et Mechant');
        $program->setSynopsis('Gros mechant');
        $program->setCategory($this->getReference('category_Animation'));
        $manager->persist($program);
        $program = new Program();
        $program->setTitle('Harry Potter');
        $program->setSynopsis('Ecole des Sorciers');
        $program->setCategory($this->getReference('category_Fantastique'));
        $manager->persist($program);
        $program = new Program();
        $program->setTitle('Scary Movie');
        $program->setSynopsis('Bouhouh');
        $program->setCategory($this->getReference('category_Horreur'));
        $manager->persist($program);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          CategoryFixtures::class,
        ];
    }


}
