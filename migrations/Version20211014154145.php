<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014154145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visitor (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ip VARCHAR(255) NOT NULL, first_visit_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_visit_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', route_name VARCHAR(255) NOT NULL, number_visit INT NOT NULL, connected TINYINT(1) NOT NULL, navigator VARCHAR(255) NOT NULL, platform VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CAE5E19FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visitor ADD CONSTRAINT FK_CAE5E19FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE visitor');
    }
}
