<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Contact information
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('email')->index();
            $table->string('phone', 30)->nullable();

            // Company information
            $table->string('company', 150)->nullable();
            $table->enum('employees', [
                '1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'
            ])->nullable();
            $table->string('title', 150)->nullable();

            // Request details
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('status', [
                'new', 'read', 'pending', 'responded', 'closed'
            ])->default('new')->index();

            // Reply information
            $table->string('reply_subject')->nullable();
            $table->text('reply_message')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->foreignUuid('replied_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Metadata
            $table->timestamps();
            $table->softDeletes();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->json('metadata')->nullable();
        });

        /**
         * FULLTEXT replacement
         */
        if (DB::getDriverName() === 'pgsql') {

            // 1. Add tsvector column
            DB::statement("
                ALTER TABLE contact_us
                ADD COLUMN search tsvector
            ");

            // 2. Populate search column
            DB::statement("
                UPDATE contact_us SET search =
                to_tsvector(
                    'simple',
                    coalesce(firstname,'') || ' ' ||
                    coalesce(lastname,'') || ' ' ||
                    coalesce(email,'') || ' ' ||
                    coalesce(subject,'') || ' ' ||
                    coalesce(message,'')
                )
            ");

            // 3. Create GIN index
            DB::statement("
                CREATE INDEX contact_us_search_idx
                ON contact_us
                USING GIN (search)
            ");
        }

        /**
         * Optional: MySQL support
         */
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE contact_us
                ADD FULLTEXT search (firstname, lastname, email, subject, message)
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS contact_us_search_idx');
        }

        Schema::dropIfExists('contact_us');
    }
};
