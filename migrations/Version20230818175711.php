<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818175711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gas_station_gas_service (gas_station_id INT NOT NULL, gas_service_id INT NOT NULL, INDEX IDX_601D3553916BFF50 (gas_station_id), INDEX IDX_601D35535D8AE483 (gas_service_id), PRIMARY KEY(gas_station_id, gas_service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gas_station_gas_service ADD CONSTRAINT FK_601D3553916BFF50 FOREIGN KEY (gas_station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station_gas_service ADD CONSTRAINT FK_601D35535D8AE483 FOREIGN KEY (gas_service_id) REFERENCES gas_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_service_gas_station DROP FOREIGN KEY FK_D11F74DD5D8AE483');
        $this->addSql('ALTER TABLE gas_service_gas_station DROP FOREIGN KEY FK_D11F74DD916BFF50');
        $this->addSql('DROP TABLE gas_service_gas_station');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gas_service_gas_station (gas_service_id INT NOT NULL, gas_station_id INT NOT NULL, INDEX IDX_D11F74DD5D8AE483 (gas_service_id), INDEX IDX_D11F74DD916BFF50 (gas_station_id), PRIMARY KEY(gas_service_id, gas_station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE gas_service_gas_station ADD CONSTRAINT FK_D11F74DD5D8AE483 FOREIGN KEY (gas_service_id) REFERENCES gas_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_service_gas_station ADD CONSTRAINT FK_D11F74DD916BFF50 FOREIGN KEY (gas_station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station_gas_service DROP FOREIGN KEY FK_601D3553916BFF50');
        $this->addSql('ALTER TABLE gas_station_gas_service DROP FOREIGN KEY FK_601D35535D8AE483');
        $this->addSql('DROP TABLE gas_station_gas_service');
    }
}
