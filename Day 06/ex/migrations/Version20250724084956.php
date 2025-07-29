<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724084956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__posts AS SELECT id, author_id, title, content, created, updated FROM posts');
        $this->addSql('DROP TABLE posts');
        $this->addSql('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, last_edited_by_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_885DBAFAD48D54E8 FOREIGN KEY (last_edited_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO posts (id, author_id, title, content, created, updated) SELECT id, author_id, title, content, created, updated FROM __temp__posts');
        $this->addSql('DROP TABLE __temp__posts');
        $this->addSql('CREATE INDEX IDX_885DBAFAF675F31B ON posts (author_id)');
        $this->addSql('CREATE INDEX IDX_885DBAFAD48D54E8 ON posts (last_edited_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__posts AS SELECT id, author_id, title, content, created, updated FROM "posts"');
        $this->addSql('DROP TABLE "posts"');
        $this->addSql('CREATE TABLE "posts" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "posts" (id, author_id, title, content, created, updated) SELECT id, author_id, title, content, created, updated FROM __temp__posts');
        $this->addSql('DROP TABLE __temp__posts');
        $this->addSql('CREATE INDEX IDX_885DBAFAF675F31B ON "posts" (author_id)');
    }
}
