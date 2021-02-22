<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222153326 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('order');

        $table->addColumn('id', Types::STRING);
        $table->addColumn('external_id', Types::STRING)->setNotnull(true);
        $table->addColumn('customer_details', Types::JSON)->setNotnull(true);
        $table->addColumn('status', Types::STRING)->setNotnull(true);
        $table->addColumn('items', Types::JSON)->setNotnull(true);
        $table->addColumn('total', Types::INTEGER)->setNotnull(true);
        $table->addColumn('created_at', Types::INTEGER)->setNotnull(true);
        $table->addColumn('updated_at', Types::INTEGER)->setNotnull(true);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('order');
    }
}
