<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251227093719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign ADD project_manager_id INT DEFAULT NULL, ADD campaign_owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD60984F51 FOREIGN KEY (project_manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD4C4CB786 FOREIGN KEY (campaign_owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1F1512DD60984F51 ON campaign (project_manager_id)');
        $this->addSql('CREATE INDEX IDX_1F1512DD4C4CB786 ON campaign (campaign_owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD60984F51');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD4C4CB786');
        $this->addSql('DROP INDEX IDX_1F1512DD60984F51 ON campaign');
        $this->addSql('DROP INDEX IDX_1F1512DD4C4CB786 ON campaign');
        $this->addSql('ALTER TABLE campaign DROP project_manager_id, DROP campaign_owner_id');
    }
}
