<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\App;

/**
 * Resolves the doctor's public identity and contact details from the DB
 * (Settings), falling back to config/translations when unset. Lets the clinic
 * edit the name, title, phone, WhatsApp and email from the dashboard and have
 * them appear across the public site. Settings are loaded once per request.
 */
class SiteInfo
{
    protected static ?array $cache = null;

    protected static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = Setting::pluck('value', 'key')->all();
        }

        return self::$cache;
    }

    /** A non-empty stored setting, or null. */
    protected static function get(string $key): ?string
    {
        $value = self::all()[$key] ?? null;

        return ($value !== null && trim((string) $value) !== '') ? (string) $value : null;
    }

    /** Forget the cache (call after a settings write within the same request). */
    public static function flush(): void
    {
        self::$cache = null;
    }

    public static function name(): string
    {
        $en = App::getLocale() === 'en';

        return ($en ? self::get('doctor_name_en') : self::get('doctor_name_ar'))
            ?? ($en ? self::get('site_name_en') : self::get('site_name_ar'))
            ?? __('heroTitle');
    }

    public static function title(): string
    {
        $en = App::getLocale() === 'en';

        return ($en ? self::get('doctor_title_en') : self::get('doctor_title_ar'))
            ?? __('doctorTitleShort');
    }

    /** Config contact array with phone/whatsapp/email overridden by settings. */
    public static function contact(): array
    {
        $contact = config('clinic.contact');
        $phone = self::get('phone') ?? $contact['phone_display'];

        return array_merge($contact, [
            'phone_display' => $phone,
            'phone_tel' => preg_replace('/\s+/', '', $phone),
            'whatsapp' => self::get('whatsapp') ?? $contact['whatsapp'],
            'email' => self::get('email') ?? $contact['email'],
        ]);
    }
}
