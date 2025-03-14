<?php

namespace BlueGrid\DataFixtures;

use BlueGrid\Entity\User;
use BlueGrid\Security\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class DirectoryFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {

    }
}
