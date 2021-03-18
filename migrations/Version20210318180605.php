<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210318180605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, cost DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6BAF7870989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_C1A8BF62C2AC5D3 (translatable_id), UNIQUE INDEX ingredient_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizza (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CFDD826F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizza_ingredient (id INT AUTO_INCREMENT NOT NULL, pizza_id INT NOT NULL, ingredient_id INT NOT NULL, `order` INT DEFAULT 1 NOT NULL, INDEX IDX_6FF6C03FD41D1D42 (pizza_id), INDEX IDX_6FF6C03F933FE08C (ingredient_id), UNIQUE INDEX pizza_ingredient_unique (pizza_id, ingredient_id), UNIQUE INDEX pizza_order_unique (pizza_id, `order`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizza_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_520E40D62C2AC5D3 (translatable_id), UNIQUE INDEX pizza_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingredient_translation ADD CONSTRAINT FK_C1A8BF62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pizza_ingredient ADD CONSTRAINT FK_6FF6C03FD41D1D42 FOREIGN KEY (pizza_id) REFERENCES pizza (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pizza_ingredient ADD CONSTRAINT FK_6FF6C03F933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pizza_translation ADD CONSTRAINT FK_520E40D62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES pizza (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient_translation DROP FOREIGN KEY FK_C1A8BF62C2AC5D3');
        $this->addSql('ALTER TABLE pizza_ingredient DROP FOREIGN KEY FK_6FF6C03F933FE08C');
        $this->addSql('ALTER TABLE pizza_ingredient DROP FOREIGN KEY FK_6FF6C03FD41D1D42');
        $this->addSql('ALTER TABLE pizza_translation DROP FOREIGN KEY FK_520E40D62C2AC5D3');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_translation');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE pizza_ingredient');
        $this->addSql('DROP TABLE pizza_translation');
    }

    /**
     * Fixes "There is no active transaction" error when using PHP 8.0
     *
     * @see https://github.com/doctrine/migrations/issues/1104
     */
    public function isTransactional(): bool
    {
        return false;
    }
}
