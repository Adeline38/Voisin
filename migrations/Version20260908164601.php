<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908164601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY `FK_AF3C6779B981C689`');
        $this->addSql('DROP INDEX IDX_AF3C6779B981C689 ON publication');
        $this->addSql('ALTER TABLE publication DROP utilisateur_id_id, CHANGE date_modification date_modification DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication ADD utilisateur_id_id INT NOT NULL, CHANGE date_modification date_modification DATETIME NOT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT `FK_AF3C6779B981C689` FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AF3C6779B981C689 ON publication (utilisateur_id_id)');
    }
}
