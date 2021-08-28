<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824185056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blocked (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, blocked TINYINT(1) NOT NULL, blocked_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', blocked_reason VARCHAR(255) NOT NULL, unblocked_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', unblocked_reason VARCHAR(255) DEFAULT NULL, INDEX IDX_DA55EB80A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rank (id INT AUTO_INCREMENT NOT NULL, rolename VARCHAR(255) NOT NULL, role JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, rank_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip VARCHAR(255) DEFAULT NULL, last_connected_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', confirmation_account TINYINT(1) NOT NULL, confirmation_account_token VARCHAR(255) DEFAULT NULL, confirmation_account_token_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', reset_token VARCHAR(255) DEFAULT NULL, reset_token_created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', reset_token_expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', reset_last_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', undeleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', connection_attempt INT NOT NULL, connection_attempt_expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', connection_attempt_def TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6497616678F (rank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_picture (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, filename VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4ED65183A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blocked ADD CONSTRAINT FK_DA55EB80A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497616678F FOREIGN KEY (rank_id) REFERENCES rank (id)');
        $this->addSql('ALTER TABLE user_picture ADD CONSTRAINT FK_4ED65183A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497616678F');
        $this->addSql('ALTER TABLE blocked DROP FOREIGN KEY FK_DA55EB80A76ED395');
        $this->addSql('ALTER TABLE user_picture DROP FOREIGN KEY FK_4ED65183A76ED395');
        $this->addSql('DROP TABLE blocked');
        $this->addSql('DROP TABLE rank');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_picture');
    }
}
