<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RevisionDocumentation;
use App\Models\RevisionEnhancement;
use App\Models\RevisionObjective;

class Revision extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'panelist_id',
        'overall_remarks',
        'capstone_stage_id',
        'approved_by',
    ];

    /**
     * Get the group this revision belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the panelist (teacher) who requested this revision.
     */
    public function panelist()
    {
        return $this->belongsTo(Teacher::class, 'panelist_id');
    }

    /**
     * Get the documentation (chapter) items for this revision.
     */
    public function documentation()
    {
        return $this->hasMany(RevisionDocumentation::class);
    }

    /**
     * Get the enhancements (IoT/system) items for this revision.
     */
    public function enhancements()
    {
        return $this->hasMany(RevisionEnhancement::class);
    }

    /**
     * Get the objectives items for this revision.
     */
    public function objectives()
    {
        return $this->hasMany(RevisionObjective::class);
    }
}