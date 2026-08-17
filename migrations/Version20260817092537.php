<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817092537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // NOTE: Add the new status column as nullable first so existing
        // users can be migrated safely.

        $this->addSql('ALTER TABLE "user" ADD status VARCHAR(20) DEFAULT NULL');

        // IMPORTANT: Convert the existing boolean status into the new
        // three-state status representation.
        //
        // true  -> active
        // false -> blocked
        $this->addSql('
            UPDATE "user"
            SET status = CASE
                WHEN is_active = true THEN \'active\'
                ELSE \'blocked\'
            END
        ');

        // NOTE: The old boolean column is no longer needed.
        $this->addSql('ALTER TABLE "user" DROP is_active');

        // IMPORTANT: From this point onward every user must have a status.
        $this->addSql('ALTER TABLE "user" ALTER COLUMN status SET NOT NULL');

        // NOTE: New users will be active unless registration logic
        // explicitly sets them to unverified.
        $this->addSql('ALTER TABLE "user" ALTER COLUMN status SET DEFAULT \'active\'');
    }

    public function down(Schema $schema): void
    {
        // NOTE: Restore the old boolean column.
        $this->addSql('ALTER TABLE "user" ADD is_active BOOLEAN DEFAULT NULL');

        // IMPORTANT: Convert the three-state status back to boolean.
        // Active = true
        // Blocked/unverified = false
        $this->addSql('
            UPDATE "user"
            SET is_active = CASE
                WHEN status = \'active\' THEN true
                ELSE false
            END
        ');

        $this->addSql('ALTER TABLE "user" ALTER COLUMN is_active SET NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP status');
    }
}
