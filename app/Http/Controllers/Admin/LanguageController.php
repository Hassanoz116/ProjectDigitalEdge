<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the application language.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage($locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        Session::put('locale', $locale);
        App::setLocale($locale);
        
        return redirect()->back();
    }
}
