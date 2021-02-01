<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210201205028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('category');
        $table->addColumn('id', Types::STRING)->setNotnull(true);
        $table->addColumn('name', Types::STRING)->setNotnull(true);
        $table->addColumn('created_at', Types::INTEGER)->setNotnull(true);
        $table->addColumn('updated_at', Types::INTEGER)->setNotnull(true);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('category');
    }
}
