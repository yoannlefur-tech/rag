<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261023140059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Extension pgvector, tables document et chunk';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS vector');

        $this->addSql(<<<'SQL'
            CREATE TABLE document (
                id          SERIAL PRIMARY KEY,
                title       VARCHAR(255) NOT NULL,
                source_type VARCHAR(10)  NOT NULL,
                created_at  TIMESTAMP    NOT NULL
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE chunk (
                id          SERIAL PRIMARY KEY,
                document_id INT NOT NULL REFERENCES document(id) ON DELETE CASCADE,
                position    INT NOT NULL,
                content     TEXT NOT NULL,
                embedding   vector(1024) NOT NULL
            )
        SQL);

        $this->addSql('CREATE INDEX chunk_embedding_idx ON chunk USING hnsw (embedding vector_cosine_ops)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE chunk');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP EXTENSION IF EXISTS vector');
    }
}
