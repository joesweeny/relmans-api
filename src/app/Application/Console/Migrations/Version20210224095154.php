<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224095154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('customer_order');
        $table->dropColumn('external_id');
    }

    public function down(Schema $schema) : void
    {
        $table = $schema->getTable('customer_order');
        $table->addColumn('external_id', Types::STRING)->setNotnull(true);
    }
}
