<?php

return [

    /*
     * The locale keys used throughout your translatable models
     * (e.g. 'ne' => Nepali, 'en' => English).
     */
    'locales' => ['ne', 'en'],

    /*
     * If a translation for the current locale is missing, Spatie will
     * fall back to this locale instead of returning an empty string.
     * Nepali is our primary/default content language.
     */
    'fallback_locale' => 'ne',

    /*
     * Set to true so a missing translation for one locale falls back to
     * WHICHEVER locale actually has content — not just the fixed
     * fallback_locale above. This lets admins post English-only or
     * Nepali-only articles and still have them show up on both domains.
     */
    'fallback_any' => true,

];