<?php

/**
 * UI Helper
 *
 * Navigation state helpers, form state helpers, flash toasts, and small UI utilities.
 * All functions are framework-friendly (CodeIgniter 4).
 */

if (!function_exists('ui_active_link')) {
    /**
     * Return "active" when the current request matches a segment or path.
     *
     * - If $segment is given: compares the FIRST URI segment (e.g., "admin", "artists").
     * - If $link is given: compares the FULL path (without domain), e.g., "admin/artists".
     * - If both are empty and current path is "/", returns "active" (home).
     *
     * @param string|null $segment First URI segment to match (e.g., "artists")
     * @param string|null $link    Full path to match (e.g., "admin/artists")
     * @return string              "active" or ""
     *
     * @usage
     *   // Active when the first segment is "artists"
     *   <li class="<?= ui_active_link('artists') ?>"><a href="<?= site_url('artists') ?>">Artists</a></li>
     *
     *   // Active when full path equals "admin/settings"
     *   <li class="<?= ui_active_link(null, 'admin/settings') ?>"><a href="<?= site_url('admin/settings') ?>">Settings</a></li>
     *
     *   // Active on homepage
     *   <li class="<?= ui_active_link('', '') ?>"><a href="<?= site_url('/') ?>">Home</a></li>
     */
    function ui_active_link(?string $segment = null, ?string $link = null): string
    {
        $req = service('request');
        $uri = $req->getUri();

        $firstSeg = trim((string) $uri->getSegment(1), '/'); // e.g., "admin"
        $path     = trim((string) $uri->getPath(), '/');      // e.g., "admin/settings"

        if ($segment !== null && $segment !== '' && $firstSeg === trim($segment, '/')) {
            return 'active';
        }
        if ($link !== null && $link !== '' && $path === trim($link, '/')) {
            return 'active';
        }
        if (($segment === null || $segment === '') && ($link === null || $link === '') && ($path === '' || $path === '/')) {
            return 'active';
        }
        return '';
    }
}

if (!function_exists('activeLink2')) {
    /**
     * Legacy: Return " active " when $segment equals $link.
     * Prefer ui_active_link() for new code.
     *
     * @param mixed $segment
     * @param mixed $link
     * @return string  " active " or " "
     *
     * @usage
     *   <li class="<?= activeLink2($current, 'artists') ?>">...</li>
     */
    function activeLink2($segment, $link) {
        return ($segment == $link) ? ' active ' : ' ';
    }
}

if (!function_exists('activeLink')) {
    /**
     * Legacy: Return "active" when $segment equals $link,
     * or when both are empty (home).
     * Prefer ui_active_link() for new code.
     *
     * @param mixed $segment
     * @param mixed $link
     * @return string "active" or ""
     *
     * @usage
     *   <li class="<?= activeLink($segment, 'events') ?>">Events</li>
     */
    function activeLink($segment, $link) {
        if (empty($segment) && empty($link)) return 'active';
        return ($segment == $link) ? 'active' : '';
    }
}

if (!function_exists('form_selected')) {
    /**
     * Return HTML " selected" when values match (for <option>).
     *
     * @param mixed $target Current value
     * @param mixed $value  Option value
     * @return string       ' selected' or ''
     *
     * @usage
     *   <option value="en"<?= form_selected($lang, 'en') ?>>English</option>
     *   <option value="ro"<?= form_selected($lang, 'ro') ?>>Română</option>
     */
    function form_selected($target, $value): string
    {
        return ((string)$target === (string)$value) ? ' selected' : '';
    }
}

if (!function_exists('form_checked')) {
    /**
     * Return HTML " checked" when values match (for checkbox/radio).
     *
     * @param mixed $target Current value
     * @param mixed $value  Input value
     * @return string       ' checked' or ''
     *
     * @usage
     *   <input type="checkbox" name="autoload" value="1"<?= form_checked($setting['autoload'] ?? '0', '1') ?>>
     *   <input type="radio" name="mode" value="dark"<?= form_checked($mode, 'dark') ?>>
     */
    function form_checked($target, $value): string
    {
        return ((string)$target === (string)$value) ? ' checked' : '';
    }
}

if (!function_exists('toast_success')) {
    /**
     * Flash a success toast message (uses session()->getFlashdata in your partial).
     *
     * @param string $message
     * @return void
     *
     * @usage
     *   toast_success('Saved successfully.');
     *   return redirect()->back();
     */
    function toast_success(string $message): void
    {
        session()->setFlashdata('success', $message);
    }
}

if (!function_exists('toast_error')) {
    /**
     * Flash an error toast message.
     *
     * @param string $message
     * @return void
     *
     * @usage
     *   toast_error('Something went wrong.');
     *   return redirect()->back();
     */
    function toast_error(string $message): void
    {
        session()->setFlashdata('error', $message);
    }
}

if (!function_exists('toast_info')) {
    /**
     * Flash an info toast message.
     *
     * @param string $message
     * @return void
     *
     * @usage
     *   toast_info('Heads up! Changes are not saved automatically.');
     */
    function toast_info(string $message): void
    {
        session()->setFlashdata('info', $message);
    }
}

if (!function_exists('badge')) {
    /**
     * Build a Bootstrap badge HTML snippet.
     *
     * @param string $text Visible text (e.g., "Active")
     * @param string $type Bootstrap color (primary|secondary|success|danger|warning|info|light|dark)
     * @return string      HTML <span> badge
     *
     * @usage
     *   echo badge('Yes', 'success');   // <span class="badge bg-success">Yes</span>
     *   echo badge('No', 'secondary');  // <span class="badge bg-secondary">No</span>
     */
    function badge(string $text, string $type = 'secondary'): string
    {
        return '<span class="badge bg-'.esc($type).'">'.esc($text).'</span>';
    }
}

if (!function_exists('btn_group')) {
    /**
     * Quick Bootstrap button group wrapper (utility).
     *
     * @param string $innerHtml Buttons HTML inside (already escaped as needed)
     * @param string $size      Bootstrap size class (e.g., 'btn-group-sm')
     * @return string           HTML <div class="btn-group ...">...</div>
     *
     * @usage
     *   echo btn_group('
     *     <a class="btn btn-warning btn-sm" href="#">Edit</a>
     *     <a class="btn btn-danger  btn-sm" href="#" onclick="return confirm(\'Delete?\')">Delete</a>
     *   ');
     */
    function btn_group(string $innerHtml, string $size = ''): string
    {
        $size = $size ? ' ' . esc($size) : '';
        return '<div class="btn-group'.$size.'" role="group">'.$innerHtml.'</div>';
    }
}
