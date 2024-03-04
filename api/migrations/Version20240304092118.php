<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304092118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update UUID mappings';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('COMMENT ON COLUMN booking.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN room.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN hotel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN booking.room_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN booking.id IS NULL');
        $this->addSql('COMMENT ON COLUMN room.id IS NULL');
        $this->addSql('COMMENT ON COLUMN hotel.id IS NULL');
        $this->addSql('COMMENT ON COLUMN booking.room_id IS NULL');

    }
}
