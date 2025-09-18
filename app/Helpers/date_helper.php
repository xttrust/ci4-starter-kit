<?php

/**
 * Date Helper
 *
 * Provides easy-to-use date/time formatting, conversions, and
 * human-friendly functions for use across the website.
 */

if (!function_exists('site_timezone')) {
    /**
     * Get the default site timezone.
     *
     * Uses get_setting('site_timezone') if available, otherwise defaults to Europe/Dublin.
     *
     * @return string Timezone identifier (e.g. "Europe/Dublin")
     *
     * @usage
     *   $tz = site_timezone(); // "Europe/Dublin"
     */
    function site_timezone(): string
    {
        return function_exists('get_setting')
            ? (string) get_setting('site_timezone', 'Europe/Dublin')
            : 'Europe/Dublin';
    }
}

if (!function_exists('dt')) {
    /**
     * Normalize input into a DateTimeImmutable object in the site timezone.
     *
     * Accepts:
     *  - int UNIX timestamp
     *  - string (MySQL "Y-m-d H:i:s" or any strtotime()-parsable string)
     *  - DateTimeInterface (DateTime, DateTimeImmutable, etc.)
     *  - null / "now"
     *
     * @param mixed       $time Input value
     * @param string|null $tz   Optional timezone (default: site timezone)
     * @return \DateTimeImmutable
     *
     * @usage
     *   $d1 = dt('2025-08-30 14:05:00');
     *   $d2 = dt(time());
     *   $d3 = dt(); // now
     */
    function dt($time = 'now', ?string $tz = null): \DateTimeImmutable
    {
        $zone = new \DateTimeZone($tz ?: site_timezone());

        if ($time instanceof \DateTimeImmutable) {
            return $time->setTimezone($zone);
        }
        if ($time instanceof \DateTimeInterface) {
            return (new \DateTimeImmutable($time->format('c')))->setTimezone($zone);
        }
        if (is_int($time)) {
            return (new \DateTimeImmutable('@' . $time))->setTimezone($zone);
        }
        if (is_string($time)) {
            $ts = strtotime($time === '' ? 'now' : $time);
            if ($ts === false) {
                $ts = time();
            }
            return (new \DateTimeImmutable('@' . $ts))->setTimezone($zone);
        }

        return (new \DateTimeImmutable('now', $zone))->setTimezone($zone);
    }
}

if (!function_exists('date_iso')) {
    /**
     * Format date into ISO 8601 (RFC3339).
     *
     * Example: 2025-08-30T14:05:00+01:00
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo date_iso('2025-08-30 14:05:00');
     */
    function date_iso($time): string
    {
        return dt($time)->format('c');
    }
}

if (!function_exists('date_db')) {
    /**
     * Format date into database-friendly string (Y-m-d H:i:s).
     *
     * Example: 2025-08-30 14:05:00
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo date_db('now'); // 2025-08-30 14:05:23
     */
    function date_db($time): string
    {
        return dt($time)->format('Y-m-d H:i:s');
    }
}

if (!function_exists('date_full')) {
    /**
     * Full verbose date with day, month, year, time.
     *
     * Example: Saturday, 30 August 2025 at 14:05
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo date_full('2025-08-30 14:05:00');
     */
    function date_full($time): string
    {
        return dt($time)->format('l, d F Y \a\t H:i');
    }
}

if (!function_exists('date_pretty')) {
    /**
     * Medium, user-friendly date.
     *
     * Example: 30 Aug 2025, 14:05
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo date_pretty(time());
     */
    function date_pretty($time): string
    {
        return dt($time)->format('d M Y, H:i');
    }
}

if (!function_exists('date_short')) {
    /**
     * Short date (day/month/year).
     *
     * Example: 30/08/2025
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo date_short('2025-08-30');
     */
    function date_short($time): string
    {
        return dt($time)->format('d/m/Y');
    }
}

if (!function_exists('time_ago')) {
    /**
     * Human-friendly "time ago" format.
     *
     * Examples:
     *   "3 hours ago"
     *   "in 2 days"
     *   "just now"
     *
     * @param mixed $time
     * @return string
     *
     * @usage
     *   echo time_ago('2025-08-30 14:05:00');
     */
    function time_ago($time): string
    {
        $now  = dt('now');
        $then = dt($time);
        $diff = $now->getTimestamp() - $then->getTimestamp();

        if ($diff === 0) return 'just now';

        $future = $diff < 0;
        $secs   = abs($diff);

        $units = [
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second',
        ];

        foreach ($units as $unitSecs => $label) {
            if ($secs >= $unitSecs) {
                $val   = (int) floor($secs / $unitSecs);
                $label .= ($val > 1) ? 's' : '';
                return $future ? "in {$val} {$label}" : "{$val} {$label} ago";
            }
        }
        return 'just now';
    }
}

if (!function_exists('human_duration')) {
    /**
     * Convert raw seconds into human duration.
     *
     * Example: 5025 → "1h 23m 45s"
     *
     * @param int $seconds
     * @return string
     *
     * @usage
     *   echo human_duration(5025);
     */
    function human_duration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        $out = [];
        if ($h) $out[] = "{$h}h";
        if ($m) $out[] = "{$m}m";
        if ($s || !$out) $out[] = "{$s}s";
        return implode(' ', $out);
    }
}
