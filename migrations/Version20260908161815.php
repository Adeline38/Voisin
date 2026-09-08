<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908161815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, photo VARCHAR(255) DEFAULT NULL, visibilite VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, utilisateur_id_id INT NOT NULL, INDEX IDX_AF3C6779B981C689 (utilisateur_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779B981C689 FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779B981C689');
        $this->addSql('DROP TABLE publication');
    }
}
