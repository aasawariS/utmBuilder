<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // drop if exists (dev only)
        DB::statement('DROP TABLE IF EXISTS `sessions`');
        DB::statement('DROP TABLE IF EXISTS `password_reset_tokens`');
        DB::statement('DROP TABLE IF EXISTS `users`');

        // users: ROWSTORE, shard by email, composite primary key (id, email) so AUTO_INCREMENT works
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `email`),
  UNIQUE KEY `users_email_unique` (`email`),
  SHARD KEY (`email`)
);
SQL
        );

        // password reset tokens: ROWSTORE, shard by email (email is primary key)
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`),
  SHARD KEY (`email`)
);
SQL
        );

        // sessions: ROWSTORE, shard by session id
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  SHARD KEY (`id`)
);
SQL
        );
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `sessions`');
        DB::statement('DROP TABLE IF EXISTS `password_reset_tokens`');
        DB::statement('DROP TABLE IF EXISTS `users`');
    }
};
