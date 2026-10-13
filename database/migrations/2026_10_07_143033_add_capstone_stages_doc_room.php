<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── evaluation_rooms.capstone_stage_id ─────────────────────────
        Schema::table('evaluation_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluation_rooms', 'capstone_stage_id')) {
                $table->unsignedBigInteger('capstone_stage_id')
                      ->nullable()
                      ->after('required_milestone_id');

                $table->foreign('capstone_stage_id')
                      ->references('id')
                      ->on('capstone_stages')
                      ->onDelete('set null');
            }
        });

        // ── certificates.capstone_stage_id ─────────────────────────────
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'capstone_stage_id')) {
                $table->unsignedBigInteger('capstone_stage_id')
                      ->nullable()
                      ->after('milestone_id');

                $table->foreign('capstone_stage_id')
                      ->references('id')
                      ->on('capstone_stages')
                      ->onDelete('set null');
            }
        });

        // ── Backfill from existing milestones ──────────────────────────
        // milestones.capstone_stage_id is VARCHAR in your schema,
        // so cast it before comparing to capstone_stages.id.
        DB::statement("
            UPDATE evaluation_rooms r
            JOIN milestones m
              ON m.id = r.required_milestone_id
            JOIN capstone_stages cs
              ON cs.id = CAST(m.capstone_stage_id AS UNSIGNED)
            SET r.capstone_stage_id = cs.id
            WHERE r.capstone_stage_id IS NULL
        ");

        DB::statement("
            UPDATE certificates c
            JOIN milestones m
              ON m.id = c.milestone_id
            JOIN capstone_stages cs
              ON cs.id = CAST(m.capstone_stage_id AS UNSIGNED)
            SET c.capstone_stage_id = cs.id
            WHERE c.capstone_stage_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('evaluation_rooms', function (Blueprint $table) {
            $table->dropForeign(['capstone_stage_id']);
            $table->dropColumn('capstone_stage_id');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['capstone_stage_id']);
            $table->dropColumn('capstone_stage_id');
        });
    }
};