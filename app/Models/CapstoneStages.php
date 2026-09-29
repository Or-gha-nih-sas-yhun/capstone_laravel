<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapstoneStages extends Model
{
        public $timestamps = false;
        protected $fillable = ['id', 'stage_title', 'is_enabled', 'is_archived', 'archived_year', 'stage_type', 'capstone_year_id'];
        
        protected $casts = [
            'is_enabled' => 'boolean',
            'is_archived' => 'boolean',
        ];

        public function capstoneYear()
        {
            return $this->belongsTo(CapstoneYear::class, 'capstone_year_id');
        }
    
        public function groups()
        {
            return $this->hasMany(Group::class, 'capstone_stage_id');
        }
            public function milestones()
            {
                return $this->hasMany(Milestone::class, 'capstone_stage_id');
            }
}
