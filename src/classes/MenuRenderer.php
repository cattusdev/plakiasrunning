<?php
class MenuRenderer extends LanguageManager
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($pages, $type)
    {
        $user = new User();
        $output = '
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <div class="logo order-1">
                <a class="navbar-brand" href="/" style="background-image:url(/assets/images/logo/logo.svg)"></a>
            </div>
            <button class="navbar-toggler order-3" type="button" id="menuToggle" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse order-2" id="main_nav">
                <ul class="navbar-nav navbar-nav-scroll">
                <li class="nav-item"><a class="nav-link" href="/#services">Υπηρεσίες</a></li>';

        $output .= $this->buildMenu($pages, '', $type);

        $output .= '
                </ul>
                <div class="navbar-social d-flex align-items-center justify-content-center">
                    <a href="https://www.instagram.com/despoinakoutsi_nutr.euphoria/" target="_blank" class="social-icon">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/people/Nutri-Euphoria/100063984672165/" target="_blank" class="social-icon">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.tiktok.com/@dkoutsi_nutritionist" target="_blank" class="social-icon">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
                <a href="/e-diet" class="ms-auto m-2" data-aos="flip-right"> 
                <button type="button" class="btn-main btn-sec">E-Diet <i class="bi bi-arrow-right-short"></i> </button> 
            </a>
                <a href="/appointments" class="m-2" data-aos="flip-right" data-aos-delay="500"> 
                <button type="button" class="btn-main">Ραντεβού <i class="bi bi-arrow-right-short"></i> </button> </a>

                <a href="/ebooks" class="m-2" data-aos="flip-right" data-aos-delay="1000"> 
                <button type="button" class="btn-main" style="background-color: #d0cdfa; border-color: #d3cffa;">Ebooks<i class="bi bi-arrow-right-short"></i> </button> 
                </a>
            </div>
           
        </div>';
        
        // Full-screen menu overlay for small screens
        $output .= '
        <div class="full-screen-menu" id="fullScreenMenu">
            <div class="vecBg"></div>
            <button class="close-fullscreen-menu" id="closeFullScreenMenu">&times;</button>

            <ul class="navbar-nav">
            <a class="navbar-brand m-0 mb-3 mt-4" href="/" style="background-image:url(/assets/images/logo/logo.svg);height: 175px !important;width: 170px !important;"></a>
            
            <li class="nav-item"><a class="nav-link" href="/#services">Υπηρεσίες</a></li>';

            // Render the menu items for full-screen mode
            $output .= $this->buildMenu($pages, '', $type);

            $output .= '
           
            
            <a href="/e-diet" class="my-2"> 
                <button type="button" class="btn-main btn-sec px-3 py-2">E-Diet</button> 
            </a>
            <a href="/appointments" class="my-2"> 
                <button type="button" class="btn-main px-3 py-2">Ραντεβού</button> 
            </a>

            <a href="/ebooks" class="my-2"> 
                <button type="button" class="btn-main px-3 py-2" style="background-color: #d0cdfa; border-color: #d3cffa;">Ebooks</button> 
            </a>
        
            <h3 class="sstitle mt-3">Ακολουθήστε μας</h3>
            <div class="navbar-social d-flex align-items-center justify-content-start gap-3">
                    <a href="https://www.instagram.com/despoinakoutsi_nutr.euphoria/" target="_blank" class="social-icon">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/people/Nutri-Euphoria/100063984672165/" target="_blank" class="social-icon">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.tiktok.com/@dkoutsi_nutritionist" target="_blank" class="social-icon">
                        <i class="bi bi-tiktok"></i>
                    </a>
            </div>

            </ul>
           
        </div>
        </nav>';


        return $output;
    }

    private function buildMenu($pages, $path = '', $type)
    {
        $html = '';

        foreach ($pages as $key => $page) {
            if ($page[$type] && $page['showOnMenu']) {

                $hasSubpages = isset($page['subpages']);
                $newPath = $path ? $path . '/' . $key : $key;

                $html .= '<li class="nav-item';
                if ($hasSubpages) {
                    $html .= ' dropdown';
                }
                $html .= '">';

                $html .= '<a class="nav-link';
                if ($hasSubpages) {
                    $html .= ' dropdown-toggle';
                }
                $html .= '" href="' . $this->config['base_url'] . $newPath . '"';
                if ($hasSubpages) {
                    $html .= ' data-bs-toggle="dropdown"';
                }
                $html .= '>' . $page['title'] . '</a>';

                if ($hasSubpages) {
                    $html .= '<ul class="dropdown-menu">';
                    $html .= $this->buildSubMenu($page['subpages'], $newPath);
                    $html .= '</ul>';
                }

                $html .= '</li>';
            }
        }

        return $html;
    }

    private function buildSubMenu($subPages, $path = '')
    {
        $html = '';

        foreach ($subPages as $key => $page) {
            $hasSubpages = isset($page['subpages']);
            $newPath = $path ? $path . '/' . $key : $key;

            $html .= '<li';
            if ($hasSubpages) {
                $html .= ' class="dropdown"';
            }
            $html .= '>';

            $html .= '<a class="dropdown-item';
            if ($hasSubpages) {
                $html .= ' dropdown-toggle';
            }
            $html .= '" href="' . $this->config['base_url'] . $newPath . '"';
            if ($hasSubpages) {
                $html .= ' data-bs-toggle="dropdown"';
            }
            $html .= '>' . $page['title'] . '</a>';

            if ($hasSubpages) {
                $html .= '<ul class="submenu dropdown-menu">';
                $html .= $this->buildSubMenu($page['subpages'], $newPath);
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }

}
