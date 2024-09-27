<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927083830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACF3193EC2 FOREIGN KEY (delivery_service_id) REFERENCES delivery_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACD8AA2833 FOREIGN KEY (payment_service_id) REFERENCES payment_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4E004AACF3193EC2 ON carts (delivery_service_id)');
        $this->addSql('CREATE INDEX IDX_4E004AACD8AA2833 ON carts (payment_service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE carts DROP CONSTRAINT FK_4E004AACF3193EC2');
        $this->addSql('ALTER TABLE carts DROP CONSTRAINT FK_4E004AACD8AA2833');
        $this->addSql('DROP INDEX IDX_4E004AACF3193EC2');
        $this->addSql('DROP INDEX IDX_4E004AACD8AA2833');
    }
}
