<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `certificates`
              MODIFY `document_type`
              ENUM('recommendation','approval','revision')
              DEFAULT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `certificates`
              MODIFY `document_type`
              ENUM('recommendation','approval')
              DEFAULT NULL
        ");
    }
};