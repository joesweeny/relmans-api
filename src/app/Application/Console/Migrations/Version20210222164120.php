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

        $table->addColumn('method', Types::JSON)->setNotnull(false);
        $table->addColumn('transaction_id', Types::STRING);
        $table->dropColumn('total');
    }

    public function down(Schema $schema) : void
    {
        $table = $schema->getTable('order');

        $table->dropColumn('method');
        $table->dropColumn('transaction_id');
        $table->addColumn('total', Types::INTEGER)->setNotnull(false);
    }
}
