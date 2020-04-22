<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = [
            [
                'username' => 'admin',
                'password' => 'admin',
                'roles' => ['ROLE_ADMIN']
            ],[
                'username' => 'user1',
                'password' => 'user1',
                'roles' => [
                    'ROLE_EDITOR',
                    'ROLE_AUTHENTICATED'
                ]
            ],[
                'username' => 'user2',
                'password' => 'user2',
                'roles' => ['ROLE_EDITOR']
            ],[
                'username' => 'user3',
                'password' => 'user3',
                'roles' => ['ROLE_AUTHENTICATED']
            ]
        ];

        foreach($users as $user_data) {
            $user = new User();

            $user->setUsername($user_data['username']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user_data['password']
            ));
            $user->setRoles($user_data['roles']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
