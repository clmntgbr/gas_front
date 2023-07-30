<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730115505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gas_station_brand (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_20AFC9BCD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gas_station ADD gas_station_brand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACF654101A FOREIGN KEY (gas_station_brand_id) REFERENCES gas_station_brand (id)');
        $this->addSql('CREATE INDEX IDX_6B3064ACF654101A ON gas_station (gas_station_brand_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACF654101A');
        $this->addSql('DROP TABLE gas_station_brand');
        $this->addSql('DROP INDEX IDX_6B3064ACF654101A ON gas_station');
        $this->addSql('ALTER TABLE gas_station DROP gas_station_brand_id');
    }
}
