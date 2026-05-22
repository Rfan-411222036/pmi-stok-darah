<?php

namespace App\Helpers;

use CodeIgniter\Filesystem\FileCollection;

class MenuHelper
{
    /**
     * Get menus based on user role from menus.json
     * 
     * @param string $role User role
     * @return array Menu items for the role
     */
    public static function getMenusByRole($role = null)
    {
        if (!$role) {
            $role = session()->get('role') ?? 'guest';
        }

        $menuJsonPath = APPPATH . 'Filters/menus.json';
        
        if (!file_exists($menuJsonPath)) {
            return [];
        }

        $jsonContent = file_get_contents($menuJsonPath);
        $menuData = json_decode($jsonContent, true);

        if (!isset($menuData['roles'])) {
            return [];
        }

        foreach ($menuData['roles'] as $roleData) {
            if ($roleData['role'] === $role) {
                return $roleData['menus'] ?? [];
            }
        }

        return [];
    }

    /**
     * Render menu items recursively
     * 
     * @param array $menus Menu items to render
     * @param string $baseUrl Base URL for links
     * @return string HTML menu items
     */
    public static function renderMenuItems($menus, $baseUrl = '')
    {
        $html = '';

        foreach ($menus as $menu) {
            $hasChildren = isset($menu['children']) && count($menu['children']) > 0;

            if ($hasChildren) {
                $html .= '<li class="nav-item has-treeview">';
                $html .= '  <a href="#" class="nav-link">';
                $html .= '    <i class="nav-icon fas fa-database"></i>';
                $html .= '    <p>';
                $html .= '      ' . htmlspecialchars($menu['name']) . '';
                $html .= '      <i class="right fas fa-angle-left"></i>';
                $html .= '    </p>';
                $html .= '  </a>';
                $html .= '  <ul class="nav nav-treeview">';
                $html .= self::renderMenuItems($menu['children'], $baseUrl);
                $html .= '  </ul>';
                $html .= '</li>';
            } else {
                $icon = self::getIconForMenu($menu['name']);
                $html .= '<li class="nav-item">';
                $html .= '  <a href="' . base_url($menu['path']) . '" class="nav-link">';
                $html .= '    <i class="nav-icon ' . $icon . '"></i>';
                $html .= '    <p>' . htmlspecialchars($menu['name']) . '</p>';
                $html .= '  </a>';
                $html .= '</li>';
            }
        }

        return $html;
    }

    /**
     * Get appropriate Font Awesome icon for menu item
     * 
     * @param string $menuName Menu name
     * @return string Font Awesome icon class
     */
    private static function getIconForMenu($menuName)
    {
        $menuName = strtolower($menuName);

        $iconMap = [
            'dashboard' => 'fas fa-tachometer-alt',
            'master data' => 'fas fa-database',
            'bank darah rumah sakit' => 'fas fa-industry',
            'rumah sakit' => 'fas fa-hospital-user',
            'stok darah' => 'fas fa-tint',
            'distribusi' => 'fas fa-truck',
            'pemusnahan' => 'fas fa-trash',
            'return' => 'fas fa-undo',
            'retur' => 'fas fa-undo',
            'user management' => 'fas fa-users',
            'cek status' => 'fas fa-check-circle',
        ];

        return $iconMap[$menuName] ?? 'fas fa-circle';
    }
}
