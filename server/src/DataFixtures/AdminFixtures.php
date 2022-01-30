<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->createAdminUser();

        $manager->persist($user);

        $manager->flush();
    }

    private function createAdminUser(): User
    {
        $user = new User();

        $user
            ->setUsername('admin')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setRoles(['ROLE_ADMIN']);

        return $user;
    }

    public static function getGroups(): array
    {
        return ['init'];
    }
}
