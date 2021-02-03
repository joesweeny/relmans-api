<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202102120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('product');

        $table->addColumn('id', Types::STRING)->setNotnull(true);
        $table->addColumn('category_id', Types::STRING)->setNotnull(true);
        $table->addColumn('name', Types::STRING)->setNotnull(true);
        $table->addColumn('status', Types::STRING)->setNotnull(true);
        $table->addColumn('created_at', Types::INTEGER)->setNotnull(true);
        $table->addColumn('updated_at', Types::INTEGER)->setNotnull(true);
        $table->setPrimaryKey(['id']);

        $table = $schema->createTable('product_price');

        $table->addColumn('id', Types::STRING)->setNotnull(true);
        $table->addColumn('product_id', Types::STRING)->setNotnull(true);
        $table->addColumn('value', Types::INTEGER)->setNotnull(true);
        $table->addColumn('size', Types::FLOAT);
        $table->addColumn('measurement', Types::STRING)->setNotnull(true);
        $table->addColumn('created_at', Types::INTEGER)->setNotnull(true);
        $table->addColumn('updated_at', Types::INTEGER)->setNotnull(true);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['product_id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('product');
        $schema->dropTable('product_price');
    }
}
