<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251231124345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign ADD description LONGTEXT DEFAULT NULL, ADD description_updated_at DATETIME DEFAULT NULL, ADD description_last_edited_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD19E99852 FOREIGN KEY (description_last_edited_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1F1512DD19E99852 ON campaign (description_last_edited_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD19E99852');
        $this->addSql('DROP INDEX IDX_1F1512DD19E99852 ON campaign');
        $this->addSql('ALTER TABLE campaign DROP description, DROP description_updated_at, DROP description_last_edited_by_id');
    }
}
