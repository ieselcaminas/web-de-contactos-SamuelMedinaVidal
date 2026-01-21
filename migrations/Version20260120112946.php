<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120112946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coche ADD propietario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coche ADD CONSTRAINT FK_A1981CD453C8D32C FOREIGN KEY (propietario_id) REFERENCES propietario (id)');
        $this->addSql('CREATE INDEX IDX_A1981CD453C8D32C ON coche (propietario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coche DROP FOREIGN KEY FK_A1981CD453C8D32C');
        $this->addSql('DROP INDEX IDX_A1981CD453C8D32C ON coche');
        $this->addSql('ALTER TABLE coche DROP propietario_id');
    }
}
