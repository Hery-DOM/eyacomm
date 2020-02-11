<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200211174506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD address LONGTEXT DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL, ADD mobil VARCHAR(255) DEFAULT NULL, ADD mail VARCHAR(255) DEFAULT NULL, ADD siret VARCHAR(255) DEFAULT NULL, ADD ape VARCHAR(255) DEFAULT NULL, ADD iban VARCHAR(255) DEFAULT NULL, ADD bic VARCHAR(255) DEFAULT NULL, ADD banque VARCHAR(255) DEFAULT NULL, ADD sepa VARCHAR(255) DEFAULT NULL, ADD date_signature DATE DEFAULT NULL, ADD ref VARCHAR(255) DEFAULT NULL, ADD offre LONGTEXT DEFAULT NULL, ADD htlocam VARCHAR(255) DEFAULT NULL, ADD dtfinlocam VARCHAR(255) DEFAULT NULL, ADD ht VARCHAR(255) DEFAULT NULL, ADD date_portabilite DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP address, DROP phone, DROP mobil, DROP mail, DROP siret, DROP ape, DROP iban, DROP bic, DROP banque, DROP sepa, DROP date_signature, DROP ref, DROP offre, DROP htlocam, DROP dtfinlocam, DROP ht, DROP date_portabilite');
    }
}
