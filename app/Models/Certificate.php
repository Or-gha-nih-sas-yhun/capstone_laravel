<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
      protected $fillable = [
        'certificate_title',
                'document_type',
        'certificate_description',
        'milestone_id',
        'capstone_stage_id',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function groupcertificate()
    {
        return $this->belongsTo(GroupCertificate::class);
    }
    public function capstoneStage()
{
    return $this->belongsTo(CapstoneStages::class, 'capstone_stage_id');
}
}
