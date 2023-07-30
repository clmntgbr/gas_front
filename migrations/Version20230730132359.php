<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730132359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station_brand ADD image_low_name VARCHAR(255) DEFAULT NULL, ADD image_low_original_name VARCHAR(255) DEFAULT NULL, ADD image_low_mime_type VARCHAR(255) DEFAULT NULL, ADD image_low_size INT DEFAULT NULL, ADD image_low_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station_brand DROP image_low_name, DROP image_low_original_name, DROP image_low_mime_type, DROP image_low_size, DROP image_low_dimensions');
    }
}
