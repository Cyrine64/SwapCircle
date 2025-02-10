<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210210733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id_categorie INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id_categorie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exchange (id_exchange INT AUTO_INCREMENT NOT NULL, id_offreur INT NOT NULL, id_demandeur INT NOT NULL, id_objet_offert INT NOT NULL, id_objet_demande INT NOT NULL, date_echange DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, INDEX IDX_D33BB07995B9D8D7 (id_offreur), INDEX IDX_D33BB079E6681A34 (id_demandeur), INDEX IDX_D33BB079D23C09DC (id_objet_offert), INDEX IDX_D33BB07943B210FB (id_objet_demande), PRIMARY KEY(id_exchange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objet (id_object INT AUTO_INCREMENT NOT NULL, id_utilisateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, etat VARCHAR(50) NOT NULL, date_ajout DATETIME NOT NULL, INDEX IDX_46CD4C38C6EE5C49 (id_utilisateur_id), PRIMARY KEY(id_object)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB07995B9D8D7 FOREIGN KEY (id_offreur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079E6681A34 FOREIGN KEY (id_demandeur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079D23C09DC FOREIGN KEY (id_objet_offert) REFERENCES objet (id_object)');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB07943B210FB FOREIGN KEY (id_objet_demande) REFERENCES objet (id_object)');
        $this->addSql('ALTER TABLE objet ADD CONSTRAINT FK_46CD4C38C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB07995B9D8D7');
        $this->addSql('ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079E6681A34');
        $this->addSql('ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079D23C09DC');
        $this->addSql('ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB07943B210FB');
        $this->addSql('ALTER TABLE objet DROP FOREIGN KEY FK_46CD4C38C6EE5C49');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE objet');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
