<?php

// database/migrations/xxxx_xx_xx_add_capstone_stage_id_to_revisions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->unsignedBigInteger('capstone_stage_id')
                  ->nullable()
                  ->after('group_id');
            $table->index(['group_id', 'capstone_stage_id'], 'rev_group_stage_idx');
            $table->index(['panelist_id', 'capstone_stage_id'], 'rev_panelist_stage_idx');
        });
    }

    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->dropIndex('rev_group_stage_idx');
            $table->dropIndex('rev_panelist_stage_idx');
            $table->dropColumn('capstone_stage_id');
        });
    }
};