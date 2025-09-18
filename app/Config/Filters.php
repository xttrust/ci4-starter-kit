<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // Shield filters
        'session'       => \CodeIgniter\Shield\Filters\SessionAuth::class,
        'tokens'        => \CodeIgniter\Shield\Filters\TokenAuth::class,
        'hmac'          => \CodeIgniter\Shield\Filters\HmacAuth::class,
        'chain'         => \CodeIgniter\Shield\Filters\ChainAuth::class,
        'auth-rates'    => \CodeIgniter\Shield\Filters\AuthRates::class,
        'group'         => \CodeIgniter\Shield\Filters\GroupFilter::class,
        'permission'    => \CodeIgniter\Shield\Filters\PermissionFilter::class,
        'force-reset'   => \CodeIgniter\Shield\Filters\ForcePasswordResetFilter::class,
        'jwt'           => \CodeIgniter\Shield\Filters\JWTAuth::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
    'before' => [
        // CSRF example (keep/adjust as you like)
        'csrf' => ['except' => ['api/*', 'webhook/*']],

        // Require session auth everywhere EXCEPT these public endpoints
        'session' => ['except' => [
            // Core public pages
            '/', 'home', 'about', 'contact', 'pricing', 'features', 'faq',

            // Legal & meta
            'privacy', 'terms', 'cookies', 'gdpr',
            'sitemap.xml', 'robots.txt', 'favicon.ico',

            // Public content
            'blog', 'blog/*', 'news', 'news/*', 'articles', 'articles/*',
            'search', 'search/*',

            // Static assets (only needed if routed via index.php)
            'assets/*', 'css/*', 'js/*', 'images/*', 'img/*', 'fonts/*', 'webfonts/*', 'uploads/*',

            // Shield auth endpoints
            'login', 'logout', 'register',
            'forgot', 'reset-password', 'reset-password/*',
            'activate-account', 'activate-account/*',
            'verify', 'verify/*', 'resend-verification',
            'magic-link/*', 'auth/*', 'webauthn/*', 'oauth/*',

            // APIs & hooks usually don’t use session auth
            'api/*', 'webhook/*', 'health', 'health/*',
        ]],
    ],
    'after' => [
        'toolbar',
    ],
];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'auth-rates' => [
            'before' => [
                'login', 
                'register', 
                'forgot', 
                'reset-password/*', 
                'activate-account/*', 
                'auth/*', 
                'api/auth/*' 
            ]
        ]
    ];
}
