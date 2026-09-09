<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260909072930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE autorisation_ami (id INT AUTO_INCREMENT NOT NULL, date_autorisation DATETIME NOT NULL, utilisateur_id INT NOT NULL, utilisateur_ami_id INT NOT NULL, INDEX IDX_F6558CF1FB88E14F (utilisateur_id), INDEX IDX_F6558CF1AD4B5080 (utilisateur_ami_id), UNIQUE INDEX UNIQ_AUTORISATION_AMI (utilisateur_id, utilisateur_ami_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, utilisateur_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_67F068BCFB88E14F (utilisateur_id), INDEX IDX_67F068BC38B217A7 (publication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE demande_ami (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, utilisateur_demandeur_id INT NOT NULL, utilisateur_receveur_id INT NOT NULL, INDEX IDX_2A51AD88FC54CAAA (utilisateur_demandeur_id), INDEX IDX_2A51AD884D81EBB5 (utilisateur_receveur_id), UNIQUE INDEX UNIQ_DEMANDE_AMI (utilisateur_demandeur_id, utilisateur_receveur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_AC6340B3FB88E14F (utilisateur_id), INDEX IDX_AC6340B338B217A7 (publication_id), UNIQUE INDEX UNIQ_LIKE_UTILISATEUR_PUBLICATION (utilisateur_id, publication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, visibilite VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME DEFAULT NULL, utilisateur_id INT NOT NULL, INDEX IDX_AF3C6779FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, photo VARCHAR(255) NOT NULL, biographie LONGTEXT DEFAULT NULL, date_inscription DATETIME NOT NULL, est_en_ligne TINYINT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_PSEUDO (pseudo), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE autorisation_ami ADD CONSTRAINT FK_F6558CF1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE autorisation_ami ADD CONSTRAINT FK_F6558CF1AD4B5080 FOREIGN KEY (utilisateur_ami_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE demande_ami ADD CONSTRAINT FK_2A51AD88FC54CAAA FOREIGN KEY (utilisateur_demandeur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE demande_ami ADD CONSTRAINT FK_2A51AD884D81EBB5 FOREIGN KEY (utilisateur_receveur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE autorisation_ami DROP FOREIGN KEY FK_F6558CF1FB88E14F');
        $this->addSql('ALTER TABLE autorisation_ami DROP FOREIGN KEY FK_F6558CF1AD4B5080');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCFB88E14F');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE demande_ami DROP FOREIGN KEY FK_2A51AD88FC54CAAA');
        $this->addSql('ALTER TABLE demande_ami DROP FOREIGN KEY FK_2A51AD884D81EBB5');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3FB88E14F');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779FB88E14F');
        $this->addSql('DROP TABLE autorisation_ami');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE demande_ami');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
