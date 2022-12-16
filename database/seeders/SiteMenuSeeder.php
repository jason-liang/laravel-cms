<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Support\Facades\DB;

class SiteMenuSeeder extends Seeder
{
    private $permissions = [
        [
            "name" => "全部权限",
            "slug" => "*",
            "http_method" => "",
            "http_path" => "*"
        ],
        [
            "name" => "访问控制面板权限",
            "slug" => "dashboard",
            "http_method" => "GET",
            "http_path" => "/"
        ],
        [
            "name" => "登录登出权限",
            "slug" => "auth.login",
            "http_method" => "",
            "http_path" => "/auth/login\r\n/auth/logout"
        ],
        [
            "name" => "用户认证权限",
            "slug" => "auth.management",
            "http_method" => "",
            "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs"
        ],
        [
            "name" => "文章权限",
            "slug" => "article.*",
            "http_method" => "ANY",
            "http_path" => "/articles*"
        ],
        [
            "name" => "栏目权限",
            "slug" => "categories.index",
            "http_method" => "GET",
            "http_path" => "/categories"
        ],
        [
            "name" => "局长信箱权限",
            "slug" => "postboxes.*",
            "http_method" => "ANY",
            "http_path" => "/postboxes"
        ]
    ];
    
    private $roles = [
        [
            "name" => "Administrator",
            "slug" => "administrator"
        ],
        [
            "name" => "部门编辑",
            "slug" => "department_editor"
        ],
        [
            "name" => "局长",
            "slug" => "general"
        ]
    ];
    
    private $menus = [
        [
            "title" => "控制面板",
            "icon" => "fa-bar-chart",
            "uri" => "/",
            "permission" => NULL
        ],
        [
            'title' => '内容管理',
            'icon' => 'fa-book',
            'uri' => 'categories',
            "permission" => NULL
        ],
        [
            'title' => '专题管理',
            'icon' => 'fa-bookmark',
            'uri' => 'topics',
            "permission" => NULL
        ],
        [
            'title' => '值班管理',
            'icon' => 'fa-address-book',
            'uri' => 'on-duties',
            "permission" => NULL
        ],
        [
            'title' => '通讯录',
            'icon' => 'fa-id-card',
            'uri' => 'contacts',
            "permission" => NULL
        ],
        [
            'title' => '局长信箱',
            'icon' => 'fa-envelope',
            'uri' => 'postboxes',
            "permission" => NULL
        ],
        [
            'title' => '文件管理',
            'icon' => 'fa-file',
            'uri' => 'media',
            "permission" => NULL
        ],
        [
            'title' => '用户管理',
            'icon' => 'fa-users',
            "uri" => "users",
            "permission" => NULL,
            "children" => [
                [
                    "title" => "用户",
                    "icon" => "fa-users",
                    "uri" => "auth/users",
                    "permission" => NULL
                ],
                [
                    "title" => "角色",
                    "icon" => "fa-user",
                    "uri" => "auth/roles",
                    "permission" => NULL
                ],
                [
                    "title" => "权限",
                    "icon" => "fa-ban",
                    "uri" => "auth/permissions",
                    "permission" => NULL
                ],
                [
                    "title" => "菜单",
                    "icon" => "fa-bars",
                    "uri" => "auth/menu",
                    "permission" => NULL
                ],
            ]
        ],
        [
            'title' => '设置',
            'icon' => 'fa-cog',
            "uri" => "settings",
            "permission" => NULL,
            "children" => [
                [
                    "title" => "网站设置",
                    "icon" => "fa-cogs",
                    "uri" => "site-settings",
                    "permission" => NULL
                ],
                [
                    "title" => "导航设置",
                    "icon" => "fa-toggle-down",
                    "uri" => "site-navbars",
                    "permission" => NULL
                ],
                [
                    "title" => "友情链接",
                    "icon" => "fa-link",
                    "uri" => "site-friend-links",
                    "permission" => NULL
                ]
            ]
        ],
        [
            "title" => "操作日志",
            "icon" => "fa-history",
            "uri" => "auth/logs",
            "permission" => NULL
        ],
    ];

    private $rolePermission = [
        'administrator' => [
            '*'
        ],
        'department_editor' => [
            'dashboard',
            'article.*',
            'categories.index'
        ],
        'general' => [
            'dashboard',
            'postboxes.*'
        ]
    ];

    private $roleMenu = [
        'administrator' => 'all',
        'department_editor' => [
            '/',
            'categories'
        ],
        'general' => [
            '/',
            'postboxes'
        ]
    ];

    public function run()
    {
        $this->createPermissions()
            ->createRoles()
            ->createMenus()
            ->addPermissionsToRoles()
            ->addRolesToMenus();
    }

    protected function createMenus() {
        $map = [];

        Menu::truncate();

        foreach ($this->menus as $mi => $menu) {
            $order = $mi + 1;
            $parent = array_merge($menu, [ 'order' => $order, 'parent_id' => 0 ]);
            $children = null;

            if (isset($parent['children']) && (!empty($parent['children']))) {
                $children = array_merge($parent['children'], []);

                unset($parent['children']);
            }
            
            $parentMenu = Menu::create($parent);
            $map[$parentMenu['uri']] = $parentMenu;
            
            if ($children) {

                foreach ($children as $ci => $child) {
                    $child_order = $ci + 1;
                    $child = array_merge($child, [ 'order' => $child_order, 'parent_id' => $parentMenu->id ]);
                    
                    $childMenu = Menu::create($child);
                    $map[$childMenu['uri']] = $childMenu;
                }
            }
        }

        $this->menuMap = $map;

        return $this;
    }

    protected function createRoles() {
        $map = [];

        Role::truncate();

        foreach ($this->roles as $role) {
            $map[$role['slug']] = Role::create($role);
        }

        $this->roleMap = $map;

        return $this;
    }

    protected function createPermissions () {
        $map = [];

        Permission::truncate();

        foreach ($this->permissions as $p) {
            $map[$p['slug']] = Permission::create($p);
        }

        $this->permissionMap = $map;

        return $this;    
    }

    protected function addPermissionsToRoles () {
        
        $data = [];

        DB::table('admin_role_permissions')->truncate();

        foreach ($this->rolePermission as $roleSlug => $permissionSlugs) {
            $role = $this->roleMap[$roleSlug];

            foreach ($permissionSlugs as $ps) {
                $permission = $this->permissionMap[$ps];


                $data[] = [
                    "role_id" => $role->id,
                    "permission_id" => $permission->id
                ];
            }
        }

        DB::table('admin_role_permissions')->insert($data);

        return $this;
    }

    protected function addRolesToMenus () {

        $data = [];

        DB::table('admin_role_menu')->truncate();

        foreach ($this->roleMenu as $roleSlug => $menuUris) {

            $role = $this->roleMap[$roleSlug];

            if ($menuUris === 'all') {

                foreach ($this->menuMap as $menu) {
                    $data[] = [
                        "role_id" => $role->id,
                        "menu_id" => $menu->id
                    ];
                }

                continue;
            }

            foreach ($menuUris as $menuUri) {

                $menu = $this->menuMap[$menuUri];

                $data[] = [
                    "role_id" => $role->id,
                    "menu_id" => $menu->id
                ];
            }
        }

        DB::table('admin_role_menu')->insert($data);

        return $this;
    }
}
