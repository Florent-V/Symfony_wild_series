<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public static int $userIndex = 0;

    public const USER_INFOS = [
        [
            'email' => 'truc@mail.fr',
            'pass' => 'azerty',
            'username' => 'user1',
            'role' => 'ROLE_CONTRIBUTOR'
        ],
        [
            'email' => 'bidule@mail.fr',
            'pass' => 'abcdef',
            'username' => 'user2',
            'role' => 'ROLE_CONTRIBUTOR'
        ],
        [
            'email' => 'machin@mail.fr',
            'pass' => 'supermdp',
            'username' => 'user2',
            'role' => 'ROLE_ADMIN',
        ]
    ];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        foreach (self::USER_INFOS as $userInfo) {
            $user = new User();
            $user->setEmail($userInfo['email']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userInfo['pass']
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(array($userInfo['role']));
            $user->setUsername($userInfo['username']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
