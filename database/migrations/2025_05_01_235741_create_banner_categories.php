<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        DB::statement("
            CREATE TABLE banner_categories (
                id CHAR(26) PRIMARY KEY,
                parent_id CHAR(26) NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description TEXT NULL,
                is_active BOOLEAN DEFAULT FALSE,
                meta_title VARCHAR(255) NULL,
                meta_description VARCHAR(255) NULL,
                locale VARCHAR(10) DEFAULT 'en',
                options JSON NULL,
                created_by UUID NULL,
                updated_by UUID NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL,

                CONSTRAINT banner_categories_parent_fk
                    FOREIGN KEY (parent_id)
                    REFERENCES banner_categories (id)
                    ON DELETE CASCADE
            )
        ");

        DB::statement("
            CREATE INDEX banner_categories_parent_id_idx
            ON banner_categories (parent_id)
        ");

        DB::statement("
            CREATE INDEX banner_categories_is_active_idx
            ON banner_categories (is_active)
        ");

        DB::statement("
            CREATE INDEX banner_categories_locale_idx
            ON banner_categories (locale)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS banner_categories");
    }
};
