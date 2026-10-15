<?php

namespace Database\Seeders;

use App\Models\LabourCategory;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            [
                'slug' => 'mason',
                'name_en' => 'Mason',
                'name_hi' => 'राजमिस्त्री',
                'skills' => [
                    ['masonry', 'Masonry', 'राजमिस्त्री'],
                    ['plaster', 'Plaster', 'प्लास्टर'],
                    ['tiles', 'Tiles', 'टाइल्स'],
                    ['brickwork', 'Brick work', 'ईंट का काम'],
                    ['concrete', 'Concrete work', 'कंक्रीट का काम'],
                    ['finishing', 'Finishing', 'फिनिशिंग'],
                ],
            ],
            [
                'slug' => 'painter',
                'name_en' => 'Painter',
                'name_hi' => 'पेंटर',
                'skills' => [
                    ['wall-painting', 'Wall painting', 'दीवार पेंटिंग'],
                    ['wood-painting', 'Wood painting', 'लकड़ी पेंटिंग'],
                    ['putty', 'Putty & primer', 'पुट्टी और प्राइमर'],
                    ['spray', 'Spray painting', 'स्प्रे पेंटिंग'],
                ],
            ],
            [
                'slug' => 'carpenter',
                'name_en' => 'Carpenter',
                'name_hi' => 'बढ़ई',
                'skills' => [
                    ['furniture', 'Furniture', 'फर्नीचर'],
                    ['doors-windows', 'Doors & windows', 'दरवाजे और खिड़की'],
                    ['modular', 'Modular work', 'मॉड्यूलर काम'],
                ],
            ],
            [
                'slug' => 'electrician',
                'name_en' => 'Electrician',
                'name_hi' => 'इलेक्ट्रिशियन',
                'skills' => [
                    ['wiring', 'Wiring', 'वायरिंग'],
                    ['fitting', 'Fittings', 'फिटिंग'],
                    ['repair', 'Repair', 'मरम्मत'],
                ],
            ],
            [
                'slug' => 'helper',
                'name_en' => 'Helper',
                'name_hi' => 'हेल्पर',
                'skills' => [
                    ['loading', 'Loading & unloading', 'लोडिंग और अनलोडिंग'],
                    ['cleaning', 'Cleaning', 'सफाई'],
                    ['general', 'General labour', 'सामान्य श्रम'],
                ],
            ],
        ];

        foreach ($catalogue as $cIndex => $category) {
            $cat = LabourCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name_en' => $category['name_en'],
                    'name_hi' => $category['name_hi'],
                    'is_active' => true,
                    'sort_order' => $cIndex + 1,
                ],
            );

            foreach ($category['skills'] as $sIndex => $skill) {
                Skill::updateOrCreate(
                    ['slug' => $skill[0]],
                    [
                        'labour_category_id' => $cat->id,
                        'name_en' => $skill[1],
                        'name_hi' => $skill[2],
                        'is_active' => true,
                        'sort_order' => $sIndex + 1,
                    ],
                );
            }
        }
    }
}
