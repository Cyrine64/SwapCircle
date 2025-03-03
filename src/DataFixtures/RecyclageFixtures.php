<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecyclageFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
    $user = new Utilisateur();
    $user->setNom('Martin');
    $user->setEmail('martin@example.com');
    $manager->persist($user);

    // Créer plusieurs recyclages
    for ($i = 1; $i <= 5; $i++) {
        $recyclage = new Recyclage();
        $recyclage->setTypeRecyclage('Papier');
        $recyclage->setDateRecyclage(new \DateTime());
        $recyclage->setCommentaire("Recyclage n°$i");
        $recyclage->setUtilisateur($user);
        $manager->persist($recyclage);
    }

    $manager->flush();
}
}
