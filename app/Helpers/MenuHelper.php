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
    public static function renderMenuItems($menus, $baseUrl = '', $isChild = false)
    {
        $html      = '';
        $currentUri = '/' . ltrim(uri_string(), '/');

        foreach ($menus as $menu) {
            $hasChildren = isset($menu['children']) && count($menu['children']) > 0;
            $label       = strtoupper($menu['name']);

            if ($hasChildren) {
                // Check if any child is active
                $childActive = false;
                foreach ($menu['children'] as $child) {
                    if (isset($child['path']) && rtrim($currentUri, '/') === rtrim($child['path'], '/')) {
                        $childActive = true;
                        break;
                    }
                }
                $openClass = $childActive ? ' menu-open' : '';
                $activeClass = $childActive ? ' active' : '';

                $icon = self::getIconForMenu($menu['name']);
                $html .= '<li class="nav-item has-treeview' . $openClass . '">';
                $html .= '  <a href="#" class="nav-link' . $activeClass . '">';
                $html .= '    <i class="nav-icon ' . $icon . '"></i>';
                $html .= '    <p>' . $label . '<i class="right fas fa-angle-left"></i></p>';
                $html .= '  </a>';
                $html .= '  <ul class="nav nav-treeview">';
                $html .= self::renderMenuItems($menu['children'], $baseUrl, true);
                $html .= '  </ul>';
                $html .= '</li>';
            } else {
                $icon      = self::getIconForMenu($menu['name']);
                $isActive  = isset($menu['path']) && rtrim($currentUri, '/') === rtrim($menu['path'], '/');
                $activeClass = $isActive ? ' active' : '';
                $indent    = $isChild ? 'pl-3' : '';

                $html .= '<li class="nav-item">';
                $html .= '  <a href="' . base_url($menu['path']) . '" class="nav-link' . $activeClass . '">';
                $html .= '    <i class="nav-icon ' . $icon . ' ' . $indent . '"></i>';
                $html .= '    <p>' . $label . '</p>';
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
            'dashboard'        => 'fas fa-tachometer-alt',
            'master data'      => 'fas fa-layer-group',
            'bdrs'             => 'fas fa-clinic-medical',
            'rumah sakit'      => 'fas fa-hospital',
            'stok darah'       => 'fas fa-tint',
            'distribusi'       => 'fas fa-truck',
            'pemusnahan'       => 'fas fa-biohazard',
            'return'           => 'fas fa-undo-alt',
            'retur'            => 'fas fa-undo-alt',
            'user management'  => 'fas fa-users-cog',
            'cek status'       => 'fas fa-check-circle',
            'supply chain'     => 'fas fa-sitemap',
            'replenishment'    => 'fas fa-boxes',
            'recall stok'      => 'fas fa-exclamation-triangle',
        ];

        return $iconMap[$menuName] ?? 'fas fa-circle';
    }
}
