<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222164120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('order');

        $table->addColumn('method', Types::JSON)->setNotnull(true);
        $table->addColumn('transaction_id', Types::STRING);
        $table->dropColumn('total');
        $table->dropColumn('items');

        $table = $schema->createTable('order_item');

        $table->addColumn('id', Types::STRING);
        $table->addColumn('order_id', Types::STRING)->setNotnull(true);
        $table->addColumn('product_id', Types::STRING)->setNotnull(true);
        $table->addColumn('name', Types::STRING)->setNotnull(true);
        $table->addColumn('price', Types::INTEGER)->setNotnull(true);
        $table->addColumn('size', Types::STRING)->setNotnull(true);
        $table->addColumn('measurement', Types::STRING)->setNotnull(true);
        $table->addColumn('quantity', Types::INTEGER)->setNotnull(true);
        $table->addColumn('created_at', Types::INTEGER)->setNotnull(true);
        $table->addColumn('updated_at', Types::INTEGER)->setNotnull(true);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $table = $schema->getTable('order');

        $table->dropColumn('method');
        $table->dropColumn('transaction_id');
        $table->addColumn('total', Types::INTEGER)->setNotnull(true);
        $table->addColumn('items', Types::JSON)->setNotnull(true);

        $schema->dropTable('order_item');
    }
}
