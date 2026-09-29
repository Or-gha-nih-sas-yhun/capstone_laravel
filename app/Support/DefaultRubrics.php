<?php

namespace App\Support;

use App\Models\Milestone;
use App\Models\Rubric;
use App\Models\RubricCriteria;

class DefaultRubrics
{
    /** milestone title (uppercase) => [rubric name, criteria] */
    public static function definitions(): array
    {
        return [
            'CAPSTONE ORAL PRESENTATION' => [
                'name' => 'Capstone 1 Oral Presentation Rubric',
                'criteria' => [
                    ['System Demo & Functional Prototype', 35],
                    ['System Architecture & Technical Design', 25],
                    ['Delivery, Presentation Skills & Clarity', 20],
                    ['Response to Panelist Q&A', 20],
                ],
            ],
            'CAPSTONE PROJECT 2 ORAL PRESENTATION' => [
                'name' => 'Capstone 2 Oral Presentation Rubric',
                'criteria' => [
                    ['Final System Quality, Usability & Completeness', 40],
                    ['Research Contribution & Evaluation Results', 25],
                    ['Presentation Delivery & Defense Performance', 20],
                    ['Technical Documentation & Design Completeness', 15],
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
        if (!$def) return false;

        if (Rubric::where('milestone_id', $milestone->id)->exists()) return false;

        $rubric = Rubric::create([
            'rubric_name'  => $def['name'],
            'milestone_id' => $milestone->id,
        ]);

        foreach ($def['criteria'] as [$name, $weight]) {
            RubricCriteria::create([
                'rubric_id'     => $rubric->id,
                'criteria_name' => $name,
                'weight'        => $weight,
                'max_score'     => 4,
            ]);
        }
        return true;
    }
}