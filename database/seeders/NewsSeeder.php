<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        $items = [
            ['title' => 'सरकारले नयाँ बजेट तयार गर्ने निर्णय गरेको छ', 'cat' => 'rajniti', 'views' => 1240],
            ['title' => 'प्रधानमन्त्रीको महत्वपूर्ण भ्रमण सम्पन्न', 'cat' => 'rajniti', 'views' => 320],
            ['title' => 'नेपाल र भारतबीच नयाँ सम्झौता हस्तान्तरण', 'cat' => 'arthatantra', 'views' => 980],
            ['title' => 'सडक विस्तार आयोजनाले गति लियो', 'cat' => 'arthatantra', 'views' => 540],
            ['title' => 'क्रिकेटमा नेपालको विजयी सफर जारी', 'cat' => 'khelkud', 'views' => 2150],
            ['title' => 'फुटबल च्याम्पियनसिपमा नेपाल सेमिफाइनलमा', 'cat' => 'khelkud', 'views' => 1560],
            ['title' => 'चलचित्र महोत्सवमा नेपाली फिल्मको छनोट', 'cat' => 'manoranjan', 'views' => 890],
            ['title' => 'राष्ट्रिय सङ्गीत पुरस्कार समारोह सम्पन्न', 'cat' => 'manoranjan', 'views' => 1120],
            ['title' => 'नयाँ मोबाइल एप्सले शिक्षा क्षेत्रमा ल्यायो क्रान्ति', 'cat' => 'prabidhi', 'views' => 1430],
            ['title' => 'नयाँ एआई प्रविधिले काम गर्ने तरिका बदल्दै', 'cat' => 'prabidhi', 'views' => 1980],
            ['title' => 'वैश्विक वायुमण्डल परिवर्तन सम्मेलन सुरु', 'cat' => 'bishwa', 'views' => 760],
            ['title' => 'संयुक्त राष्ट्रसंघको महासभा बैठक सम्पन्न', 'cat' => 'bishwa', 'views' => 590],
            ['title' => 'स्वास्थ्य मन्त्रालयद्वारा नयाँ खोप अभियान घोषणा', 'cat' => 'swasthya', 'views' => 670],
            ['title' => 'नयाँ प्रादेशिक अस्पताल निर्माण सम्पन्न', 'cat' => 'swasthya', 'views' => 380],
        ];

        foreach ($items as $index => $item) {
            $category = Category::where('slug', $item['cat'])->first();
            if (!$category) {
                continue;
            }

            $slug = Str::slug($item['title']);
            if (empty($slug)) {
                $slug = 'news-' . uniqid();
            }

            News::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'title' => $item['title'],
                    'excerpt' => 'यो समाचारको छोटो सारांश हो। थप जानकारीको लागि पूरा समाचार पढ्नुहोस्।',
                    'body' => "यो समाचारको पूरा विवरण हो।\n\nयहाँ थप जानकारी र विवरण थपिनेछ। यो एउटा demo content हो जुन admin panel बाट सम्पादन गर्न सकिन्छ।",
                    'is_published' => true,
                    'published_at' => now()->subHours($index),
                    'views' => $item['views'],
                ]
            );
        }
    }
}