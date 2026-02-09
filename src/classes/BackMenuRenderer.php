<?php
class BackMenuRenderer
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($pages, $type)
    {
        $output = '<div class="c-sidebar">' .
        '<div class="logo">' .
        '<a class="sidebar-logo m-1" href="/" style="background-image:url(/assets/images/app_logo.svg)"></a>' .
        '</div>' .
        '<div class="hamtoggle d-md-none">' .
        '<div class="js-hamburger">' .
        '<div class="hamburger-toggle"><span class="bar-top"></span><span class="bar-mid"></span><span class="bar-bot"></span></div>' .
        '</div>' .
        '</div>' .
        '<div class="c-sidebar__content">' .
        '<nav class="side-menu js-menu">' .
        '<ul class="u-list my-2">';

        $output .= $this->buildMenu($pages, $type);

        $output .= '</ul>' .
        '</nav>' .
        '</div>' .
        '</div>';

        return $output;
    }

    private function buildMenu($pages, $type, $parentPath = '')
    {
        $html = '';
        $mainUser = new User();
        foreach ($pages as $key => $page) {
            if ($page[$type] && $page['showOnMenu'] && $mainUser->hasPermission($page['allowTo'])) {

                $hasSubpages = isset($page['subpages']);
                $newPath = $parentPath ? $parentPath . '/' . $key : $key;

                $html .= '<li class="side-menu__item';
                if ($hasSubpages) {
                    $html .= ' has-submenu';
                }
                $html .= '">';

                $html .= '<div class="side-menu__item__inner" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="'. $page['title'] .'">';
                $html .= '<a class="d-flex align-items-center main-ref"  href="' . $this->config['base_url']  . $newPath . '">';
                $html .= '<i class="' . $page['icon'] . '"></i>';
                $html .= '<div class="side-menu-item__title">';
                $html .= '<span>' . $page['title'] . '</span>';
                $html .= '</div>';
                $html .= '</a>';

                if ($hasSubpages) {
                    $html .= '<div class="side-menu-item__expand js-expand-submenu">';
                    $html .= '<i class="bi bi-chevron-down"></i>';
                    $html .= '</div>';
                }

                $html .= '</div>';

                if ($hasSubpages) {
                    $html .= '<ul class="side-menu__submenu u-list side-menu-collapsed" style="display: none;">';
                    $html .= '<li><a data-name="' . $key . '" href="' . $this->config['base_url']  . $newPath . '" style="display:none" class="collapsed-ref tmp-ref"><i class="pe-2 ' . $page["icon"] . '"></i>' . $page['title'] . '</a></li>';
                    $html .= $this->buildSubMenu($page['subpages'], $type, $newPath);
                    $html .= '</ul>';
                }

                $html .= '</li>';
            }
        }

        return $html;
    }

    private function buildSubMenu($subPages, $type, $parentPath)
    {
        $html = '';

        foreach ($subPages as $key => $page) {
            $hasSubpages = isset($page['subpages']);
            $newPath = $parentPath . '/' . $key;

            if ($page['showOnMenu']) {
                # code...
           
            $html .= '<li>';

            $html .= '<a class="collapsed-ref" data-name="' . $key . '" href="' . $this->config['base_url'] . $newPath . '">';
            $html .= '<i class="' . $page['icon'] . ' pe-1"></i>' . $page['title'];
            $html .= '</a>';

            if ($hasSubpages) {
                $html .= '<ul class="side-menu__submenu u-list">';
                $html .= $this->buildSubMenu($page['subpages'], $type, $newPath);
                $html .= '</ul>';
            }

            $html .= '</li>';
            }
        }

        return $html;
    }
}

