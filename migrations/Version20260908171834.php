<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908171834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE autorisation_ami (id INT AUTO_INCREMENT NOT NULL, date_autorisation DATETIME NOT NULL, utilisateur_id INT NOT NULL, utilisateur_ami_id INT NOT NULL, INDEX IDX_F6558CF1FB88E14F (utilisateur_id), INDEX IDX_F6558CF1AD4B5080 (utilisateur_ami_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE autorisation_ami ADD CONSTRAINT FK_F6558CF1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE autorisation_ami ADD CONSTRAINT FK_F6558CF1AD4B5080 FOREIGN KEY (utilisateur_ami_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE autorisation_ami DROP FOREIGN KEY FK_F6558CF1FB88E14F');
        $this->addSql('ALTER TABLE autorisation_ami DROP FOREIGN KEY FK_F6558CF1AD4B5080');
        $this->addSql('DROP TABLE autorisation_ami');
    }
}
