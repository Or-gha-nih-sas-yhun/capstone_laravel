<?php

namespace App\Support;

use App\Models\Milestone;
use App\Models\Rubric;
use App\Models\RubricCriteria;

class DefaultRubrics
{
    /**
     * Milestone title (uppercase) => [rubric name, criteria names].
     *
     * Every criterion is scored on a 1–4 scale; the "max_score" column is
     * always 4 so it no longer needs to be stored here.
     */
    public static function definitions(): array
    {
        return [
            'CAPSTONE ORAL PRESENTATION' => [
                'name' => 'Capstone 1 Oral Presentation Rubric',
                'criteria' => [
                    'System Demo & Functional Prototype',
                    'System Architecture & Technical Design',
                    'Delivery, Presentation Skills & Clarity',
                    'Response to Panelist Q&A',
                ],
            ],
            'CAPSTONE PROJECT 2 ORAL PRESENTATION' => [
                'name' => 'Capstone 2 Oral Presentation Rubric',
                'criteria' => [
                    'Final System Quality, Usability & Completeness',
                    'Research Contribution & Evaluation Results',
                    'Presentation Delivery & Defense Performance',
                    'Technical Documentation & Design Completeness',
                ],
            ],
        ];
    }

    /**
     * Attach the default rubric to a milestone if its title matches
     * and it has no rubric yet. Returns true if a rubric was created.
     */
    public static function attachTo(Milestone $milestone): bool
    {
        $def = self::definitions()[strtoupper(trim($milestone->milestone_title))] ?? null;
        if (!$def) {
            return false;
        }

        if (Rubric::where('milestone_id', $milestone->id)->exists()) {
            return false;
        }

        $rubric = Rubric::create([
            'rubric_name'  => $def['name'],
            'milestone_id' => $milestone->id,
        ]);

        foreach ($def['criteria'] as $criteriaName) {
            RubricCriteria::create([
                'rubric_id'     => $rubric->id,
                'criteria_name' => $criteriaName,
                'max_score'     => 4,
            ]);
        }

        return true;
    }
}