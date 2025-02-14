<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214102856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog (id_article INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL, PRIMARY KEY(id_article)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_like (id_blog_like INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_article INT NOT NULL, action VARCHAR(50) NOT NULL, PRIMARY KEY(id_blog_like)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id_commentaire INT AUTO_INCREMENT NOT NULL, id_article INT NOT NULL, id_utilisateur INT NOT NULL, id_objet INT NOT NULL, contenu LONGTEXT NOT NULL, date_commentaire DATETIME NOT NULL, PRIMARY KEY(id_commentaire)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE echange (id_echange INT AUTO_INCREMENT NOT NULL, id_objet INT NOT NULL, id_utilisateur INT NOT NULL, name_echange VARCHAR(255) NOT NULL, image_echange VARCHAR(255) NOT NULL, date_echange DATETIME NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id_echange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objet (id_objet INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, etat VARCHAR(50) NOT NULL, date_ajout DATETIME NOT NULL, image VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, INDEX IDX_46CD4C3850EAE44 (id_utilisateur), PRIMARY KEY(id_objet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id_reclamation INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_objet INT NOT NULL, message LONGTEXT NOT NULL, statut VARCHAR(50) NOT NULL, type_reclamation VARCHAR(50) NOT NULL, date_reclamation DATETIME NOT NULL, PRIMARY KEY(id_reclamation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage (id_recyclage INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_objet INT NOT NULL, type_recyclage VARCHAR(50) NOT NULL, date_recyclage DATETIME NOT NULL, commentaire LONGTEXT NOT NULL, PRIMARY KEY(id_recyclage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id_reponse INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, id_utilisateur INT NOT NULL, contenu LONGTEXT NOT NULL, date_reponse DATETIME NOT NULL, PRIMARY KEY(id_reponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial (id_tutorial INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, vid_url VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, id_recyclage INT NOT NULL, id_utilisateur INT NOT NULL, PRIMARY KEY(id_tutorial)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE objet ADD CONSTRAINT FK_46CD4C3850EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE objet DROP FOREIGN KEY FK_46CD4C3850EAE44');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE blog_like');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE echange');
        $this->addSql('DROP TABLE objet');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE recyclage');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE tutorial');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
