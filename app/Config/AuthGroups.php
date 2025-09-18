<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        // Admin shell
        'admin.access'         => 'Access the admin area',
        'admin.settings'       => 'Manage global settings',
        'system.maintain'      => 'Run maintenance ops (cache, queues, etc.)',
        'logs.view'            => 'View application logs',
        'audit.view'           => 'View audit/activity logs',
        'backups.manage'       => 'Create/download backups',
        'jobs.manage'          => 'Manage background jobs/queue',

        // Users & Access
        'users.view'           => 'View users list/details',
        'users.create'         => 'Create users',
        'users.edit'           => 'Edit users',
        'users.delete'         => 'Delete users',
        'users.manage-admins'  => 'Assign admin roles & permissions',

        // Media Library
        'media.view'           => 'Browse/download media',
        'media.upload'         => 'Upload files',
        'media.edit'           => 'Edit media metadata',
        'media.delete'         => 'Delete media',

        // Pages (CMS)
        'pages.view'           => 'View pages',
        'pages.create'         => 'Create pages',
        'pages.edit'           => 'Edit pages',
        'pages.delete'         => 'Delete pages',
        'pages.publish'        => 'Publish/unpublish pages',

        // Menus (navigation)
        'menus.view'           => 'View menus',
        'menus.create'         => 'Create menus/items',
        'menus.edit'           => 'Edit menus/items',
        'menus.delete'         => 'Delete menus/items',

        // Blog/News (optional but common)
        'posts.view'           => 'View posts',
        'posts.create'         => 'Create posts',
        'posts.edit'           => 'Edit posts',
        'posts.delete'         => 'Delete posts',
        'posts.publish'        => 'Publish/unpublish posts',

        // Forms & Submissions
        'forms.view'           => 'View forms & submissions',
        'forms.create'         => 'Create forms',
        'forms.edit'           => 'Edit forms',
        'forms.delete'         => 'Delete forms',

        // SEO / Redirects
        'seo.view'             => 'View SEO settings',
        'seo.manage'           => 'Manage SEO & redirects',

        // Newsletter
        'newsletter.view'      => 'View subscribers/lists',
        'newsletter.manage'    => 'Manage lists and exports',

        // Integrations / API / Webhooks
        'integrations.view'    => 'View integrations',
        'integrations.manage'  => 'Manage API keys/webhooks',

        // Notifications (templates & sends)
        'notifications.view'   => 'View notification templates',
        'notifications.manage' => 'Manage templates & sends',

        // i18n
        'i18n.view'            => 'View languages/translations',
        'i18n.manage'          => 'Manage languages/translations',

        // Analytics (read-only dashboards)
        'analytics.view'       => 'View analytics dashboards',

        // SaaS (optional)
        'billing.view'         => 'View billing data',
        'billing.manage'       => 'Manage plans, invoices, subs',
        'tenants.view'         => 'View tenants',
        'tenants.manage'       => 'Manage tenants and domains',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'system.*',
            'logs.*',
            'audit.*',
            'backups.*',
            'jobs.*',
            'users.*',
            'media.*',
            'pages.*',
            'menus.*',
            'posts.*',
            'forms.*',
            'seo.*',
            'newsletter.*',
            'integrations.*',
            'notifications.*',
            'i18n.*',
            'analytics.*',
            'billing.*',
            'tenants.*',
        ],

        'admin' => [
            'admin.access',
            // Ops (limited)
            'audit.view',
            'logs.view',
            'analytics.view',

            // Users (no delete by default)
            'users.view', 'users.create', 'users.edit',

            // Content & media
            'media.view', 'media.upload', 'media.edit', 'media.delete',
            'pages.view','pages.create','pages.edit','pages.publish',
            'menus.view','menus.create','menus.edit',
            'posts.view','posts.create','posts.edit','posts.publish',
            'forms.view','forms.manage',
            'seo.view','seo.manage',
            'newsletter.view','newsletter.manage',
            'integrations.view',
            'notifications.view',
            'i18n.view','i18n.manage',
        ],

        'developer' => [
            'admin.access',
            'admin.settings',
            'system.maintain',
            'jobs.manage',
            'logs.view',
            'backups.manage',
            'integrations.manage',
            'media.view','media.upload','media.edit',
            // usually devs need to touch pages/posts during build:
            'pages.view','pages.edit',
            'posts.view','posts.edit',
            'seo.view',
        ],

        'editor' => [ // optional role for content teams
            'admin.access',
            'media.view','media.upload',
            'pages.view','pages.create','pages.edit','pages.publish',
            'posts.view','posts.create','posts.edit','posts.publish',
            'menus.view','menus.edit',
            'seo.view','seo.manage',
        ],

        'viewer' => [ // read-only internal role
            'admin.access',
            'media.view','pages.view','posts.view','menus.view',
            'analytics.view',
        ],

        'user' => [],

        'beta' => ['beta.access'],
    ];
}
