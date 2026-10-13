<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Revision;
use App\Models\Group;

return new class extends Migration
{
    public function up(): void
    {
        // Every revision that was created before we started storing the stage.
        $orphans = Revision::whereNull('capstone_stage_id')->get();

        foreach ($orphans as $rev) {
            $group = Group::with('room')->find($rev->group_id);

            // 1) Prefer the stage attached to the group's current room.
            $stageId = $group?->room?->capstone_stage_id;

            // 2) Fallback: the active Capstone 2 stage for that year.
            if (! $stageId) {
                $stageId = DB::table('capstone_stages')
                    ->where('stage_type', 2)
                    ->where('is_archived', false)
                    ->where(function ($q) use ($group) {
                        $q->where('capstone_year_id', $group?->capstone_year_id)
                          ->orWhereNull('capstone_year_id');
                    })
                    ->orderByDesc('is_enabled')   // prefer the enabled one
                    ->value('id');
            }

            if ($stageId) {
                $rev->capstone_stage_id = $stageId;
                $rev->save();
            }
        }
    }

    public function down(): void
    {
        // No-op. We don't want to un-heal data.
    }
};