<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210830162015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD author_edit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E667BC250E6 FOREIGN KEY (author_edit_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E667BC250E6 ON article (author_edit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E667BC250E6');
        $this->addSql('DROP INDEX IDX_23A0E667BC250E6 ON article');
        $this->addSql('ALTER TABLE article DROP author_edit_id');
    }
}
