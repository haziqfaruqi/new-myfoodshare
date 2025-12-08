<?php

namespace App\Helpers;

use App\Models\Setting;

class LogoHelper
{
    /**
     * Get the site logo URL
     */
    public static function getLogoUrl()
    {
        $logo = Setting::get('site_logo');
        return $logo ? asset($logo) : null;
    }

    /**
     * Check if a custom logo exists
     */
    public static function hasCustomLogo()
    {
        return !empty(Setting::get('site_logo'));
    }

    /**
     * Get logo HTML
     */
    public static function getLogoHtml($height = 'h-8', $fallbackIcon = 'leaf')
    {
        if (self::hasCustomLogo()) {
            return '<img src="' . self::getLogoUrl() . '" alt="Logo" class="' . $height . ' w-auto">';
        }

        $iconClasses = $height . ' fill-current';

        switch ($fallbackIcon) {
            case 'user-circle':
                return '<i data-lucide="user-circle" class="' . $iconClasses . '"></i>';
            case 'leaf':
            default:
                return '<i data-lucide="leaf" class="' . $iconClasses . '"></i>';
        }
    }
}