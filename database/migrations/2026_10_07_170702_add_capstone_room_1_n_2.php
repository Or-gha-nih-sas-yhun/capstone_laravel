<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /// database/migrations/xxxx_add_capstone_room_columns_to_groups.php
Schema::table('groups', function (Blueprint $table) {
    $table->foreignId('capstone1_room_id')->nullable()->after('room_id')
          ->constrained('evaluation_rooms')->nullOnDelete();
    $table->foreignId('capstone2_room_id')->nullable()->after('capstone1_room_id')
          ->constrained('evaluation_rooms')->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['capstone1_room_id']);
            $table->dropForeign(['capstone2_room_id']);
            $table->dropColumn(['capstone1_room_id', 'capstone2_room_id']);
        });
    }
};
