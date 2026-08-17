<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260817102334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add email verification token and expiration fields to users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE "user" ADD verification_token VARCHAR(64) DEFAULT NULL'
        );

        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_USER_VERIFICATION_TOKEN ON "user" (verification_token)'
        );

        $this->addSql(
            'ALTER TABLE "user" ADD verification_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE "user" DROP verification_token_expires_at'
        );

        $this->addSql(
            'DROP INDEX UNIQ_USER_VERIFICATION_TOKEN'
        );

        $this->addSql(
            'ALTER TABLE "user" DROP verification_token'
        );
    }
}