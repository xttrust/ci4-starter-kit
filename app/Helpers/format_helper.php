<?php

/**
 * Format Helper
 *
 * Common formatting utilities: money, bytes, slugs, excerpts,
 * random strings, percentages, filenames, truncation, padding.
 */

if (!function_exists('money')) {
    /**
     * Format a number as currency with a symbol.
     *
     * Pulls default currency from settings: get_setting('payment_currency', 'EUR') if available.
     * Supported: EUR (€), USD ($), GBP (£).
     *
     * @param float       $amount   Numeric amount (e.g., 19.9)
     * @param string|null $currency Optional ISO code ('EUR','USD','GBP')
     * @return string     Formatted currency string
     *
     * @usage
     *   echo money(19.9);          // "€19.90"
     *   echo money(19.9, 'USD');   // "$19.90"
     *   echo money(1234.5);        // "€1,234.50"
     */
    function money(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?: (function_exists('get_setting') ? get_setting('payment_currency', 'EUR') : 'EUR');
        $symbol = '€';
        if ($currency === 'USD') $symbol = '$';
        elseif ($currency === 'GBP') $symbol = '£';

        return $symbol . number_format($amount, 2, '.', ',');
    }
}

if (!function_exists('bytes')) {
    /**
     * Convert raw bytes to a human-readable string.
     *
     * Uses binary units: B, KB, MB, GB, TB.
     *
     * @param int $bytes     Input size in bytes
     * @param int $precision Decimal places
     * @return string
     *
     * @usage
     *   echo bytes(1536000);        // "1.47 MB"
     *   echo bytes(1048576, 1);     // "1.0 MB"
     *   echo bytes(500);            // "500 B"
     */
    function bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B','KB','MB','GB','TB'];
        $bytes = max($bytes, 0);
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('slugify')) {
    /**
     * Create a URL-friendly slug from text.
     *
     * @param string $text Input text
     * @param string $sep  Separator
     * @return string      Slug
     *
     * @usage
     *   echo slugify('Hello World!');        // "hello-world"
     *   echo slugify('Știință & Cercetare'); // "stiinta-cercetare"
     */
    function slugify(string $text, string $sep = '-'): string
    {
        $text = preg_replace('~[^\pL\d]+~u', $sep, $text);
        $text = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $sep);
        $text = preg_replace('~-+~', $sep, $text);
        return strtolower($text ?: 'n-a');
    }
}

if (!function_exists('excerpt')) {
    /**
     * Word-safe excerpt with ellipsis.
     *
     * @param string $text Text input (HTML stripped)
     * @param int    $max  Max characters
     * @return string
     *
     * @usage
     *   echo excerpt('This is a longer sentence.', 10);  // "This is…"
     */
    function excerpt(string $text, int $max = 140): string
    {
        $t = trim(strip_tags($text));
        if (mb_strlen($t) <= $max) return $t;
        $cut = mb_substr($t, 0, $max);
        $space = mb_strrpos($cut, ' ');
        if ($space !== false) $cut = mb_substr($cut, 0, $space);
        return rtrim($cut) . '…';
    }
}

if (!function_exists('rand_str')) {
    /**
     * Generate a cryptographically secure, URL-safe random string.
     *
     * @param int $len Number of random bytes (not final string length)
     * @return string
     *
     * @usage
     *   echo rand_str();       // e.g., "Qy6b1wStYb93akzE"
     *   echo rand_str(32);     // longer token
     */
    function rand_str(int $len = 16): string
    {
        return rtrim(strtr(base64_encode(random_bytes($len)), '+/', '-_'), '=');
    }
}

if (!function_exists('percent')) {
    /**
     * Format ratio as a percentage.
     *
     * @param float $ratio    Ratio (0.0–1.0)
     * @param int   $decimals Decimal places
     * @return string
     *
     * @usage
     *   echo percent(0.1234);   // "12.34%"
     *   echo percent(0.5, 0);   // "50%"
     */
    function percent(float $ratio, int $decimals = 2): string
    {
        return number_format($ratio * 100, $decimals) . '%';
    }
}

if (!function_exists('clean_filename')) {
    /**
     * Sanitize a filename for safe storage.
     *
     * Removes special characters, keeps alphanumerics, dash, underscore, dot.
     *
     * @param string $name Original filename
     * @return string      Safe filename
     *
     * @usage
     *   echo clean_filename('My File (final).pdf'); // "My-File-final.pdf"
     */
    function clean_filename(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9\.\-_]/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        return trim($name, '-');
    }
}

if (!function_exists('truncate_words')) {
    /**
     * Truncate text by word count.
     *
     * @param string $text  Input text
     * @param int    $words Max number of words
     * @return string       Truncated text
     *
     * @usage
     *   echo truncate_words('The quick brown fox jumps over the lazy dog', 4);
     *   // "The quick brown fox…"
     */
    function truncate_words(string $text, int $words = 20): string
    {
        $arr = preg_split('/\s+/', strip_tags($text));
        if (count($arr) <= $words) return implode(' ', $arr);
        return implode(' ', array_slice($arr, 0, $words)) . '…';
    }
}

if (!function_exists('pad_left')) {
    /**
     * Pad a string on the left to a given length.
     *
     * @param string $str  Input string
     * @param int    $len  Total length
     * @param string $char Padding character
     * @return string
     *
     * @usage
     *   echo pad_left('42', 5, '0');   // "00042"
     *   echo pad_left('abc', 6, '-');  // "---abc"
     */
    function pad_left(string $str, int $len, string $char = '0'): string
    {
        return str_pad($str, $len, $char, STR_PAD_LEFT);
    }
}
