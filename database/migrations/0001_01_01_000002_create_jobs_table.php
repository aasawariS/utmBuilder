<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // DEV: drop if exists (safe for development). If in production, do a non-destructive migration instead.
        DB::statement('DROP TABLE IF EXISTS `failed_jobs`');
        DB::statement('DROP TABLE IF EXISTS `job_batches`');
        DB::statement('DROP TABLE IF EXISTS `jobs`');

        // jobs table - ROWSTORE, shard by id
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` INT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`),
  SHARD KEY (`id`)
);
SQL
        );

        // job_batches table - ROWSTORE, shard by id (string)
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL,
  PRIMARY KEY (`id`),
  SHARD KEY (`id`)
);
SQL
        );

        // failed_jobs table - ROWSTORE, shard by uuid so UNIQUE(uuid) is allowed
        // composite PRIMARY KEY (id, uuid) keeps AUTO_INCREMENT on id and satisfies shard-key unique rules
        DB::statement(<<<'SQL'
CREATE ROWSTORE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `uuid`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  SHARD KEY (`uuid`)
);
SQL
        );
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `failed_jobs`');
        DB::statement('DROP TABLE IF EXISTS `job_batches`');
        DB::statement('DROP TABLE IF EXISTS `jobs`');
    }
};
