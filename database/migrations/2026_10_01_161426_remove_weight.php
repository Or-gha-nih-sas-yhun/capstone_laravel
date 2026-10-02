<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Make `weight` nullable on `rubric_criteria`.
     * Required because weights are no longer collected by the rubric forms.
     */
    public function up(): void
    {
        Schema::table('rubric_criteria', function (Blueprint $table) {
            $table->decimal('weight', 5, 2)->nullable()->change();
        });
    }

    /**
     * Revert: bring back the NOT NULL constraint.
     * Any NULL rows are first backfilled to 0 so the constraint can apply.
     */
    public function down(): void
    {
        DB::table('rubric_criteria')->whereNull('weight')->update(['weight' => 0]);

        Schema::table('rubric_criteria', function (Blueprint $table) {
            $table->decimal('weight', 5, 2)->nullable(false)->change();
        });
    }
};