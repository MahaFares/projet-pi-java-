<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209212557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_category CHANGE icon icon VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commandes RENAME INDEX fk_35d4282cf7384557 TO IDX_35D4282CF7384557');
        $this->addSql('ALTER TABLE guide CHANGE rating rating DOUBLE PRECISION DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE hebergement CHANGE image_principale image_principale VARCHAR(500) DEFAULT NULL, CHANGE label_eco label_eco VARCHAR(100) DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY `fk_paiement_commande`');
        $this->addSql('ALTER TABLE paiement CHANGE id_commande id_commande INT DEFAULT NULL, CHANGE date_paiement date_paiement DATETIME NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E3E314AE8 FOREIGN KEY (id_commande) REFERENCES commandes (id_commande)');
        $this->addSql('ALTER TABLE paiement RENAME INDEX fk_paiement_commande TO IDX_B1DC7A1E3E314AE8');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY `fk_produit_categorie`');
        $this->addSql('ALTER TABLE produits CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8CC9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
        $this->addSql('ALTER TABLE produits RENAME INDEX fk_produit_categorie TO IDX_BE2DDF8CC9486A13');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE activity_category CHANGE icon icon VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE commandes RENAME INDEX idx_35d4282cf7384557 TO FK_35D4282CF7384557');
        $this->addSql('ALTER TABLE guide CHANGE rating rating DOUBLE PRECISION DEFAULT \'NULL\', CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE hebergement CHANGE image_principale image_principale VARCHAR(500) DEFAULT \'NULL\', CHANGE label_eco label_eco VARCHAR(100) DEFAULT \'NULL\', CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E3E314AE8');
        $this->addSql('ALTER TABLE paiement CHANGE date_paiement date_paiement DATETIME DEFAULT \'current_timestamp()\', CHANGE id_commande id_commande INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT `fk_paiement_commande` FOREIGN KEY (id_commande) REFERENCES commandes (id_commande) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement RENAME INDEX idx_b1dc7a1e3e314ae8 TO fk_paiement_commande');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8CC9486A13');
        $this->addSql('ALTER TABLE produits CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits RENAME INDEX idx_be2ddf8cc9486a13 TO fk_produit_categorie');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
