<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Egypt
            'EG' => [
                ['name_en' => 'Cairo', 'name_ar' => 'القاهرة'],
                ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية'],
                ['name_en' => 'Giza', 'name_ar' => 'الجيزة'],
                ['name_en' => 'Sharm El Sheikh', 'name_ar' => 'شرم الشيخ'],
                ['name_en' => 'Hurghada', 'name_ar' => 'الغردقة'],
            ],
            // Saudi Arabia
            'SA' => [
                ['name_en' => 'Riyadh', 'name_ar' => 'الرياض'],
                ['name_en' => 'Jeddah', 'name_ar' => 'جدة'],
                ['name_en' => 'Mecca', 'name_ar' => 'مكة'],
                ['name_en' => 'Medina', 'name_ar' => 'المدينة'],
                ['name_en' => 'Dammam', 'name_ar' => 'الدمام'],
            ],
            // UAE
            'AE' => [
                ['name_en' => 'Dubai', 'name_ar' => 'دبي'],
                ['name_en' => 'Abu Dhabi', 'name_ar' => 'أبوظبي'],
                ['name_en' => 'Sharjah', 'name_ar' => 'الشارقة'],
                ['name_en' => 'Ajman', 'name_ar' => 'عجمان'],
                ['name_en' => 'Ras Al Khaimah', 'name_ar' => 'رأس الخيمة'],
            ],
            // Kuwait
            'KW' => [
                ['name_en' => 'Kuwait City', 'name_ar' => 'مدينة الكويت'],
                ['name_en' => 'Hawalli', 'name_ar' => 'حولي'],
                ['name_en' => 'Salmiya', 'name_ar' => 'السالمية'],
                ['name_en' => 'Farwaniya', 'name_ar' => 'الفروانية'],
            ],
            // Qatar
            'QA' => [
                ['name_en' => 'Doha', 'name_ar' => 'الدوحة'],
                ['name_en' => 'Al Rayyan', 'name_ar' => 'الريان'],
                ['name_en' => 'Al Wakrah', 'name_ar' => 'الوكرة'],
            ],
            // Bahrain
            'BH' => [
                ['name_en' => 'Manama', 'name_ar' => 'المنامة'],
                ['name_en' => 'Riffa', 'name_ar' => 'الرفاع'],
                ['name_en' => 'Muharraq', 'name_ar' => 'المحرق'],
            ],
            // Oman
            'OM' => [
                ['name_en' => 'Muscat', 'name_ar' => 'مسقط'],
                ['name_en' => 'Salalah', 'name_ar' => 'صلالة'],
                ['name_en' => 'Sohar', 'name_ar' => 'صحار'],
            ],
            // Jordan
            'JO' => [
                ['name_en' => 'Amman', 'name_ar' => 'عمان'],
                ['name_en' => 'Zarqa', 'name_ar' => 'الزرقاء'],
                ['name_en' => 'Irbid', 'name_ar' => 'إربد'],
                ['name_en' => 'Aqaba', 'name_ar' => 'العقبة'],
            ],
            // Lebanon
            'LB' => [
                ['name_en' => 'Beirut', 'name_ar' => 'بيروت'],
                ['name_en' => 'Tripoli', 'name_ar' => 'طرابلس'],
                ['name_en' => 'Sidon', 'name_ar' => 'صيدا'],
            ],
             // Syria
            'SY' => [
                ['name_en' => 'Damascus', 'name_ar' => 'دمشق'],
                ['name_en' => 'Latakia', 'name_ar' => 'اللاذقية'],
                ['name_en' => 'Homs', 'name_ar' => 'حمص'],
            ],
            // Palestine
            'PS' => [
                ['name_en' => 'Ramallah', 'name_ar' => 'رام الله'],
                ['name_en' => 'Nablus', 'name_ar' => 'نابلس'],
            ],
        ];

        foreach ($cities as $countryCode => $countryCities) {
            $country = Country::where('code', $countryCode)->first();
            
            if ($country) {
                foreach ($countryCities as $city) {
                    City::firstOrCreate(
                        [
                            'name_en' => $city['name_en'],
                            'country_id' => $country->id
                        ],
                        [
                            'name_ar' => $city['name_ar'],
                            'country_id' => $country->id
                        ]
                    );
                }
            }
        }
    }
}
