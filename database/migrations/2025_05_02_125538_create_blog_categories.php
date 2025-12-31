<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
    DB::statement("
        CREATE TABLE blog_categories (
            id CHAR(26) PRIMARY KEY,
            parent_id CHAR(26) NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            description TEXT NULL,
            is_active BOOLEAN DEFAULT FALSE,
            meta_title VARCHAR(60) NULL,
            meta_description VARCHAR(160) NULL,
            locale VARCHAR(10) DEFAULT 'en',
            options JSON NULL,
            created_by UUID NULL,
            updated_by UUID NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,

            CONSTRAINT blog_categories_parent_fk
                FOREIGN KEY (parent_id)
                REFERENCES blog_categories (id)
                ON DELETE CASCADE,

            CONSTRAINT blog_categories_slug_locale_unique
                UNIQUE (slug, locale)
        )
    ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS blog_categories");
    }
};
