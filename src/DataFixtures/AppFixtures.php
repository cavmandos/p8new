<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\Clock\now;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setUsername("admin");
        $admin->setEmail("admin@mail.com");
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "1234"));
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        // User 1
        $user1 = new User();
        $user1->setUsername("user-one");
        $user1->setEmail("user-one@mail.com");
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1, "1234"));
        $user1->setRoles(["ROLE_USER"]);
        $manager->persist($user1);

        // User 2
        $user2 = new User();
        $user2->setUsername("user-two");
        $user2->setEmail("user-two@mail.com");
        $user2->setPassword($this->userPasswordHasher->hashPassword($user2, "1234"));
        $user2->setRoles(["ROLE_USER"]);
        $manager->persist($user2);

        $randomUser = [$admin, $user1, $user2];

        $randomTitle = [
            0 => "Sortir le toutou",
            1 => "Faire les courses",
            2 => "Appeler la belle-mère",
            3 => "Anniversaire de machin",
            4 => "Réviser soutenance",
            5 => "Faire le ménage"
        ];

        $randomData = [
            0 => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ut ultricies est.",
            1 => "Mauris aliquam nisi fringilla nisi luctus, vel pulvinar nisi hendrerit.",
            2 => "Nam quis nibh arcu. Ut dui erat"
        ];

        for ($i = 1; $i < 25; $i++) {
            $task = new Task();
            $task->setCreatedAt(now());
            $titleRandom = array_rand($randomTitle);
            $task->setTitle($randomTitle[$titleRandom]);
            $dataRandom = array_rand($randomData);
            $task->setContent($randomData[$dataRandom]);
            $task->setIsDone((bool) rand(0, 1));
            $random = array_rand($randomUser);
            $task->setUserId($randomUser[$random]);
            $manager->persist($task);
        }

        $manager->flush();

    }
}
