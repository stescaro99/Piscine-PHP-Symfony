<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250724VoteTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Vote table and remove likes column from Post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, post_id INTEGER NOT NULL, type VARCHAR(10) NOT NULL, CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A1085644B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A108564A76ED395 ON vote (user_id)');
        $this->addSql('CREATE INDEX IDX_5A1085644B89032C ON vote (post_id)');
        $this->addSql('ALTER TABLE posts DROP COLUMN likes');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE posts ADD COLUMN likes INTEGER DEFAULT 0 NOT NULL');
        $this->addSql('DROP TABLE vote');
    }
}
