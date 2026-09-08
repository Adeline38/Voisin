<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908170536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_ami (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, utilisateur_demandeur_id INT NOT NULL, utilisateur_receveur_id INT NOT NULL, INDEX IDX_2A51AD88FC54CAAA (utilisateur_demandeur_id), INDEX IDX_2A51AD884D81EBB5 (utilisateur_receveur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE demande_ami ADD CONSTRAINT FK_2A51AD88FC54CAAA FOREIGN KEY (utilisateur_demandeur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE demande_ami ADD CONSTRAINT FK_2A51AD884D81EBB5 FOREIGN KEY (utilisateur_receveur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_ami DROP FOREIGN KEY FK_2A51AD88FC54CAAA');
        $this->addSql('ALTER TABLE demande_ami DROP FOREIGN KEY FK_2A51AD884D81EBB5');
        $this->addSql('DROP TABLE demande_ami');
    }
}
