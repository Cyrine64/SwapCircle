<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Admin');
        $utilisateur->setPrenom('User');
        $utilisateur->setEmail('admin@swapcircle.com');
        $utilisateur->setMotDePasse('admin123');
        $utilisateur->setRole('ROLE_ADMIN');
        $utilisateur->setDateInscription(new \DateTime());

        $manager->persist($utilisateur);
        $manager->flush();
    }
}
