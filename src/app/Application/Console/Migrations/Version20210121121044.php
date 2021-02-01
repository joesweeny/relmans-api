<?php

declare(strict_types=1);

namespace IntelligenceFusion\Actor\Application\Console\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210121121044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'This is a sample migration please do not use - execute bin/console migrations:generate to generate a new file';
    }

    public function up(Schema $schema) : void
    {

    }

    public function down(Schema $schema) : void
    {

    }
}
