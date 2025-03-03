<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214140659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog (id_article INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL, INDEX IDX_C015514350EAE44 (id_utilisateur), PRIMARY KEY(id_article)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_like (id_blog_like INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_article INT NOT NULL, action VARCHAR(50) NOT NULL, INDEX IDX_4CB3CC2350EAE44 (id_utilisateur), INDEX IDX_4CB3CC23DCA7A716 (id_article), PRIMARY KEY(id_blog_like)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id_commentaire INT AUTO_INCREMENT NOT NULL, id_article INT NOT NULL, id_utilisateur INT NOT NULL, id_objet INT NOT NULL, contenu LONGTEXT NOT NULL, date_commentaire DATETIME NOT NULL, INDEX IDX_67F068BCDCA7A716 (id_article), INDEX IDX_67F068BC50EAE44 (id_utilisateur), INDEX IDX_67F068BCA8480D08 (id_objet), PRIMARY KEY(id_commentaire)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE echange (id_echange INT AUTO_INCREMENT NOT NULL, id_objet INT NOT NULL, id_utilisateur INT NOT NULL, name_echange VARCHAR(255) NOT NULL, image_echange VARCHAR(255) NOT NULL, date_echange DATETIME NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_B577E3BFA8480D08 (id_objet), INDEX IDX_B577E3BF50EAE44 (id_utilisateur), PRIMARY KEY(id_echange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objet (id_objet INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, etat VARCHAR(50) NOT NULL, date_ajout DATETIME NOT NULL, image VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, INDEX IDX_46CD4C3850EAE44 (id_utilisateur), PRIMARY KEY(id_objet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id_reclamation INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, message LONGTEXT NOT NULL, statut VARCHAR(50) NOT NULL, type_reclamation VARCHAR(50) NOT NULL, date_reclamation DATETIME NOT NULL, titre VARCHAR(255) NOT NULL, INDEX IDX_CE60640450EAE44 (id_utilisateur), PRIMARY KEY(id_reclamation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage (id_recyclage INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_objet INT NOT NULL, type_recyclage VARCHAR(50) NOT NULL, date_recyclage DATETIME NOT NULL, commentaire LONGTEXT NOT NULL, INDEX IDX_C737D7D550EAE44 (id_utilisateur), INDEX IDX_C737D7D5A8480D08 (id_objet), PRIMARY KEY(id_recyclage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id_reponse INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, id_utilisateur INT NOT NULL, contenu LONGTEXT NOT NULL, date_reponse DATETIME NOT NULL, INDEX IDX_5FB6DEC7D672A9F3 (id_reclamation), INDEX IDX_5FB6DEC750EAE44 (id_utilisateur), PRIMARY KEY(id_reponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial (id_tutorial INT AUTO_INCREMENT NOT NULL, id_recyclage INT NOT NULL, id_utilisateur INT NOT NULL, description LONGTEXT NOT NULL, vid_url VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_C66BFFE947026BF2 (id_recyclage), INDEX IDX_C66BFFE950EAE44 (id_utilisateur), PRIMARY KEY(id_tutorial)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C015514350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE blog_like ADD CONSTRAINT FK_4CB3CC2350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE blog_like ADD CONSTRAINT FK_4CB3CC23DCA7A716 FOREIGN KEY (id_article) REFERENCES blog (id_article)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCDCA7A716 FOREIGN KEY (id_article) REFERENCES blog (id_article)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA8480D08 FOREIGN KEY (id_objet) REFERENCES objet (id_objet)');
        $this->addSql('ALTER TABLE echange ADD CONSTRAINT FK_B577E3BFA8480D08 FOREIGN KEY (id_objet) REFERENCES objet (id_objet)');
        $this->addSql('ALTER TABLE echange ADD CONSTRAINT FK_B577E3BF50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE objet ADD CONSTRAINT FK_46CD4C3850EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640450EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE recyclage ADD CONSTRAINT FK_C737D7D550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE recyclage ADD CONSTRAINT FK_C737D7D5A8480D08 FOREIGN KEY (id_objet) REFERENCES objet (id_objet)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7D672A9F3 FOREIGN KEY (id_reclamation) REFERENCES reclamation (id_reclamation)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC750EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE tutorial ADD CONSTRAINT FK_C66BFFE947026BF2 FOREIGN KEY (id_recyclage) REFERENCES recyclage (id_recyclage)');
        $this->addSql('ALTER TABLE tutorial ADD CONSTRAINT FK_C66BFFE950EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog DROP FOREIGN KEY FK_C015514350EAE44');
        $this->addSql('ALTER TABLE blog_like DROP FOREIGN KEY FK_4CB3CC2350EAE44');
        $this->addSql('ALTER TABLE blog_like DROP FOREIGN KEY FK_4CB3CC23DCA7A716');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCDCA7A716');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC50EAE44');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA8480D08');
        $this->addSql('ALTER TABLE echange DROP FOREIGN KEY FK_B577E3BFA8480D08');
        $this->addSql('ALTER TABLE echange DROP FOREIGN KEY FK_B577E3BF50EAE44');
        $this->addSql('ALTER TABLE objet DROP FOREIGN KEY FK_46CD4C3850EAE44');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640450EAE44');
        $this->addSql('ALTER TABLE recyclage DROP FOREIGN KEY FK_C737D7D550EAE44');
        $this->addSql('ALTER TABLE recyclage DROP FOREIGN KEY FK_C737D7D5A8480D08');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7D672A9F3');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC750EAE44');
        $this->addSql('ALTER TABLE tutorial DROP FOREIGN KEY FK_C66BFFE947026BF2');
        $this->addSql('ALTER TABLE tutorial DROP FOREIGN KEY FK_C66BFFE950EAE44');
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
