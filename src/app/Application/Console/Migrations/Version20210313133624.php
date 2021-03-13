<?php

declare(strict_types=1);

namespace Relmans\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313133624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('customer_order_item');
        $table->addIndex(['order_id'], 'customer_order_item_order_id');
    }

    public function down(Schema $schema) : void
    {
        $table = $schema->getTable('customer_order_item');
        $table->dropIndex('customer_order_item_order_id');
    }
}
