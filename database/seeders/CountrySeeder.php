<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name_en' => 'Egypt', 'name_ar' => 'مصر', 'code' => 'EG', 'phone_code' => '+20'],
            ['name_en' => 'Saudi Arabia', 'name_ar' => 'السعودية', 'code' => 'SA', 'phone_code' => '+966'],
            ['name_en' => 'United Arab Emirates', 'name_ar' => 'الإمارات', 'code' => 'AE', 'phone_code' => '+971'],
            ['name_en' => 'Kuwait', 'name_ar' => 'الكويت', 'code' => 'KW', 'phone_code' => '+965'],
            ['name_en' => 'Qatar', 'name_ar' => 'قطر', 'code' => 'QA', 'phone_code' => '+974'],
            ['name_en' => 'Bahrain', 'name_ar' => 'البحرين', 'code' => 'BH', 'phone_code' => '+973'],
            ['name_en' => 'Oman', 'name_ar' => 'عمان', 'code' => 'OM', 'phone_code' => '+968'],
            ['name_en' => 'Jordan', 'name_ar' => 'الأردن', 'code' => 'JO', 'phone_code' => '+962'],
            ['name_en' => 'Lebanon', 'name_ar' => 'لبنان', 'code' => 'LB', 'phone_code' => '+961'],
            ['name_en' => 'Iraq', 'name_ar' => 'العراق', 'code' => 'IQ', 'phone_code' => '+964'],
            ['name_en' => 'Syria', 'name_ar' => 'سوريا', 'code' => 'SY', 'phone_code' => '+963'],
            ['name_en' => 'Palestine', 'name_ar' => 'فلسطين', 'code' => 'PS', 'phone_code' => '+970'],
            ['name_en' => 'Yemen', 'name_ar' => 'اليمن', 'code' => 'YE', 'phone_code' => '+967'],
            ['name_en' => 'Libya', 'name_ar' => 'ليبيا', 'code' => 'LY', 'phone_code' => '+218'],
            ['name_en' => 'Tunisia', 'name_ar' => 'تونس', 'code' => 'TN', 'phone_code' => '+216'],
            ['name_en' => 'Algeria', 'name_ar' => 'الجزائر', 'code' => 'DZ', 'phone_code' => '+213'],
            ['name_en' => 'Morocco', 'name_ar' => 'المغرب', 'code' => 'MA', 'phone_code' => '+212'],
            ['name_en' => 'Sudan', 'name_ar' => 'السودان', 'code' => 'SD', 'phone_code' => '+249'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
