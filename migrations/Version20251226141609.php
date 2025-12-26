<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251226141609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, budget DOUBLE PRECISION NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, platform_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_1F1512DDFFE6496F (platform_id), INDEX IDX_1F1512DD19EB6921 (client_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDFFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DDFFE6496F');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD19EB6921');
        $this->addSql('DROP TABLE campaign');
    }
}
