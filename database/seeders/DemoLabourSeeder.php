<?php

namespace Database\Seeders;

use App\Models\LabourAvailability;
use App\Models\LabourCategory;
use App\Models\LabourDetail;
use App\Models\LabourRate;
use App\Models\LabourSkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoLabourSeeder extends Seeder
{
    public function run(): void
    {
        $category = LabourCategory::where('slug', 'mason')->first();
        $categoryId = $category?->id;

        $user = User::firstOrCreate(
            ['mobile' => '9876543210'],
            ['role' => User::ROLE_LABOUR, 'status' => 'active', 'preferred_locale' => 'hi'],
        );

        $labour = LabourDetail::firstOrCreate(
            ['user_id' => $user->id],
            [
                'labour_category_id' => $categoryId,
                'full_name' => 'राहुल कुमार',
                'gender' => 'male',
                'date_of_birth' => now()->subYears(28)->toDateString(),
                'address_line' => 'गली नंबर 5, राम नगर',
                'city' => 'सीकर',
                'district' => 'सीकर',
                'state' => 'राजस्थान',
                'pincode' => '332001',
                'latitude' => 27.6094,
                'longitude' => 75.1399,
                'max_travel_distance_km' => 10,
                'experience_years' => 5,
                'work_type' => 'full_time',
                'bio' => 'मैं सभी प्रकार के राजमिस्त्री का काम कर सकता हूँ।',
                'registration_step' => 6,
                'is_available_today' => true,
                'profile_completed_at' => now(),
            ],
        );

        // Skills.
        $masonSkills = Skill::whereIn('slug', ['masonry', 'plaster', 'tiles', 'brickwork'])->pluck('id');
        LabourSkill::where('labour_detail_id', $labour->id)->delete();
        foreach ($masonSkills as $index => $skillId) {
            LabourSkill::create([
                'labour_detail_id' => $labour->id,
                'skill_id' => $skillId,
                'is_primary' => $index === 0,
            ]);
        }

        // Rates.
        $rates = ['hour' => 150, 'day' => 700, 'week' => 4500, 'month' => 15000];
        foreach ($rates as $unit => $amount) {
            LabourRate::updateOrCreate(
                ['labour_detail_id' => $labour->id, 'rate_unit' => $unit],
                ['amount' => $amount, 'is_active' => true],
            );
        }

        // Weekly availability (Mon-Sun = 1-7), Saturday unavailable.
        for ($day = 1; $day <= 7; $day++) {
            LabourAvailability::updateOrCreate(
                ['labour_detail_id' => $labour->id, 'day_of_week' => $day],
                [
                    'is_available' => $day !== 6,
                    'start_time' => $day !== 6 ? '09:00' : null,
                    'end_time' => $day !== 6 ? '18:00' : null,
                ],
            );
        }
    }
}
