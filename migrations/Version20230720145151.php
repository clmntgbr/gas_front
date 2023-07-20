<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720145151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency CHANGE slug reference VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gas_service CHANGE slug reference VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gas_type DROP google_id, DROP url, DROP website, DROP phone_number, DROP place_id, DROP compound_code, DROP global_code, DROP google_rating, DROP rating, DROP user_ratings_total, DROP icon, DROP wheelchair_accessible_entrance, DROP business_status, DROP opening_hours, DROP slug, CHANGE reference reference VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE google_place ADD google_id VARCHAR(15) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD website VARCHAR(255) DEFAULT NULL, ADD phone_number VARCHAR(20) DEFAULT NULL, ADD place_id VARCHAR(50) DEFAULT NULL, ADD compound_code VARCHAR(50) DEFAULT NULL, ADD global_code VARCHAR(50) DEFAULT NULL, ADD google_rating VARCHAR(10) DEFAULT NULL, ADD rating VARCHAR(10) DEFAULT NULL, ADD user_ratings_total VARCHAR(10) DEFAULT NULL, ADD icon VARCHAR(255) DEFAULT NULL, ADD wheelchair_accessible_entrance VARCHAR(255) DEFAULT NULL, ADD business_status VARCHAR(50) DEFAULT NULL, ADD opening_hours LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD name VARCHAR(255) NOT NULL, ADD reference VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE google_place DROP google_id, DROP url, DROP website, DROP phone_number, DROP place_id, DROP compound_code, DROP global_code, DROP google_rating, DROP rating, DROP user_ratings_total, DROP icon, DROP wheelchair_accessible_entrance, DROP business_status, DROP opening_hours, DROP name, DROP reference');
        $this->addSql('ALTER TABLE gas_type ADD google_id VARCHAR(15) DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD website VARCHAR(255) DEFAULT NULL, ADD phone_number VARCHAR(20) DEFAULT NULL, ADD place_id VARCHAR(50) DEFAULT NULL, ADD compound_code VARCHAR(50) DEFAULT NULL, ADD global_code VARCHAR(50) DEFAULT NULL, ADD google_rating VARCHAR(10) DEFAULT NULL, ADD rating VARCHAR(10) DEFAULT NULL, ADD user_ratings_total VARCHAR(10) DEFAULT NULL, ADD icon VARCHAR(255) DEFAULT NULL, ADD wheelchair_accessible_entrance VARCHAR(255) DEFAULT NULL, ADD business_status VARCHAR(50) DEFAULT NULL, ADD opening_hours LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD slug VARCHAR(255) NOT NULL, CHANGE reference reference VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE currency CHANGE reference slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gas_service CHANGE reference slug VARCHAR(255) NOT NULL');
    }
}
