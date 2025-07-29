<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724084826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts ADD COLUMN updated DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__posts AS SELECT id, author_id, title, content, created FROM "posts"');
        $this->addSql('DROP TABLE "posts"');
        $this->addSql('CREATE TABLE "posts" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created DATETIME NOT NULL, CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "posts" (id, author_id, title, content, created) SELECT id, author_id, title, content, created FROM __temp__posts');
        $this->addSql('DROP TABLE __temp__posts');
        $this->addSql('CREATE INDEX IDX_885DBAFAF675F31B ON "posts" (author_id)');
    }
}
