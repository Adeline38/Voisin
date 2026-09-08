<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908172539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_AC6340B3FB88E14F (utilisateur_id), INDEX IDX_AC6340B338B217A7 (publication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3FB88E14F');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('DROP TABLE `like`');
    }
}
