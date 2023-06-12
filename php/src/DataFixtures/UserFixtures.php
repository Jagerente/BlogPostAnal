<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createAuthor());
        $manager->persist($this->createModerator());
        $manager->persist($this->createGuest());

        $manager->flush();
    }

    private function createAuthor(): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'qwe123'
        );

        $user->setUsername("Author")
            ->setEmail("author@example.com")
            ->setPassword($hashedPassword)
            ->setRoles([RoleEnum::Author]);

        return $user;
    }
    private function createModerator(): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'qwe123'
        );
        $user->setUsername("Moderator")
            ->setEmail("moderator@example.com")
            ->setPassword($hashedPassword)
            ->setRoles([RoleEnum::Moderator]);

        return $user;
    }
    private function createGuest(): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'qwe123'
        );
        $user->setUsername("Guest")
            ->setEmail("guest@example.com")
            ->setPassword($hashedPassword)
            ->setRoles([RoleEnum::User]);

        return $user;
    }
}