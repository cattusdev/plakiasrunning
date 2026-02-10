<?php
class PageController
{
    private $pages;

    public function __construct()
    {
        $this->pages = [
            'home' => [
                'title' => 'Αρχική',
                'home_page' => true,
                'template' => 'home.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Alma | Κέντρο Ψυχολογίας & Προσωπικής Ανάπτυξης | Ζάκυνθος',
                    'description' => 'Στο Alma, η φροντίδα της ψυχής συναντά το άλμα προς την εξέλιξη. Ένας χώρος για να κατανοήσεις τον εαυτό σου και να προχωρήσεις στην αλλαγή και την ισορροπία.',
                    'keywords' => 'ψυχολογία, ψυχοθεραπεία, ψυχολόγος Ζάκυνθος, προσωπική ανάπτυξη, συμβουλευτική, άγχος, αυτογνωσία, ψυχική υγεία, online συνεδρίες, psychologist Zakynthos',
                ],
            ],
            'ebooks' => [
                'title' => 'E-book',
                'home_page' => true,
                'template' => 'ebooks.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Κλείσε Ραντεβού | Online & Δια Ζώσης Συνεδρίες | NutrEphoria',
                    'description' => 'Κλείσε το ραντεβού σου τώρα. Επίλεξε online ή δια ζώσης συνεδρία και ξεκίνησε το ταξίδι σου προς μια καλύτερη διατροφή.',
                    'keywords' => 'ραντεβού διαιτολόγος, online συνεδρία, διατροφή συνεδρίες, nutrition appointment, διαιτολόγος Ζάκυνθος, κλινική διατροφή, αθλητική διατροφή',
                ],
            ],
            'services' => [
                'title' => 'Υπηρεσίες',
                'home_page' => false,
                'template' => 'services.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Υπηρεσίες Ψυχοθεραπείας & Συμβουλευτικής | Alma',
                    'description' => 'Εξατομικευμένες υπηρεσίες ψυχικής υγείας για ενήλικες, γονείς και παιδιά. Ψυχοθεραπεία, συμβουλευτική γονέων και workshops προσωπικής ανάπτυξης.',
                    'keywords' => 'υπηρεσίες ψυχολογίας, ατομική ψυχοθεραπεία, συμβουλευτική γονέων, θεραπεία ζεύγους, ψυχοθεραπεία παιδιών, workshops ψυχολογίας, συνεδρίες online',
                ],
            ],
            'about' => [
                'title' => 'Σχετικά',
                'home_page' => false,
                'template' => 'about.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Ποιοι Είμαστε | Η Φιλοσοφία του Alma',
                    'description' => 'Alma σημαίνει Ψυχή και Άλμα. Γνωρίστε την ομάδα μας και το όραμά μας: να δημιουργήσουμε το χώρο όπου γεννιούνται τα όνειρα και συμβαίνει η αλλαγή.',
                    'keywords' => 'σχετικά με εμάς, κέντρο alma, φιλοσοφία ψυχοθεραπείας, βιογραφικό ψυχολόγου, ομάδα ψυχολόγων, Ζάκυνθος',
                ],
            ],
            'contact' => [
                'title' => 'Επικοινωνία',
                'home_page' => false,
                'template' => 'contact.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Επικοινωνία | Κλείστε Ραντεβού | Alma',
                    'description' => 'Κάντε το πρώτο βήμα για την προσωπική σας εξέλιξη. Επικοινωνήστε με το κέντρο Alma στη Ζάκυνθο για ραντεβού δια ζώσης ή online.',
                    'keywords' => 'επικοινωνία ψυχολόγος, ραντεβού ψυχοθεραπείας, τηλέφωνο ψυχολόγου Ζάκυνθος, διεύθυνση alma, contact psychologist',
                ],
            ],
            'prop_login' => [
                'title' => 'Σύνδεση',
                'home_page' => false,
                'template' => 'login.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => "",
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => false,
                "showFooter" => false,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            '2Auth' => [
                'title' => 'Two-factor authentication',
                'home_page' => false,
                'template' => '2fa.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => "",
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => false,
                "showFooter" => false,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            'logout' => [
                'title' => 'Logout',
                'home_page' => false,
                'template' => 'logout.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => "",
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => false,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            'legal' => [
                'name' => 'legal',
                'home_page' => false,
                'title'  => 'Privacy Policy',
                'template' => 'legal.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            'payments-policy' => [
                'name' => 'payments-policy',
                'home_page' => false,
                'title'  => 'Payments Policy',
                'template' => 'payments.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => 'Τρόποι Πληρωμής & Πολιτική Ακύρωσης | Alma',
                    'description' => 'Ενημερωθείτε για τους τρόπους πληρωμής των συνεδριών και την πολιτική ακύρωσης του κέντρου Alma. Ασφαλείς συναλλαγές μέσω EveryPay.',
                    'keywords' => 'τρόποι πληρωμής, κόστος συνεδρίας, πολιτική ακύρωσης, επιστροφή χρημάτων, πληρωμή ψυχοθεραπείας',
                ],
            ],
            'cookies-policy' => [
                'name' => 'cookies-policy',
                'home_page' => false,
                'title'  => 'Cookies Policy',
                'template' => 'cookies.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => '',
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            'unsubscribe' => [
                'name' => 'unsubscribe',
                'home_page' => false,
                'title'  => 'Απεγγραφή',
                'template' => 'unsubscribe.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => "",
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "Απεγγραφή",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            'download-view' => [
                'name' => 'downloadebook',
                'home_page' => false,
                'title'  => 'Λήψη',
                'template' => 'download_view.php',
                'frontEnd' => true,
                'backEnd' => false,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => false,
                "allowTo" => "",
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "",
                "icon_color" => "",
                'meta' => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                ],
            ],
            //// BACKEND
            'profile' => [
                'title' => 'Προφίλ',
                'home_page' => false,
                'template' => 'profile.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin', 'editor', 'user'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => false,
                "showOnFooter" => false,
                "icon" => "bi bi-person-square",
                "icon_color" => ""
            ],
            'appointments-calendar' => [
                'title' => 'Ημερολόγιο/Διαθεσιμότητα',
                'home_page' => false,
                'template' => 'slot_calendar.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-calendar-week-fill",
                "icon_color" => ""
            ],
            'bookings' => [
                'title' => 'Κρατήσεις',
                'home_page' => false,
                'template' => 'bookings.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-journal-bookmark-fill",
                "icon_color" => ""
            ],
            'packages' => [
                'title' => 'Πακέτα Ραντεβού',
                'home_page' => false,
                'template' => 'packages.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-box-fill",
                "icon_color" => ""
            ],
            // 'digital-products' => [
            //     'title' => 'Digital Products',
            //     'home_page' => false,
            //     'template' => 'digital_products.php',
            //     'frontEnd' => false,
            //     'backEnd' => true,
            //     "bodyClass" => "",
            //     "wrapperClass" => "",
            //     "class" => "",
            //     "reqLogin" => true,
            //     "allowTo" => array('admin'),
            //     "public" => true,
            //     "hasCategories" => false,
            //     "showNavbar" => true,
            //     "showFooter" => true,
            //     "sidebar" => false,
            //     "showOnMenu" => true,
            //     "showOnFooter" => false,
            //     "icon" => "bi bi-cloud-arrow-down",
            //     "icon_color" => ""
            // ],
            // 'digital-orders' => [
            //     'title' => 'Digital Orders',
            //     'home_page' => false,
            //     'template' => 'digital_orders.php',
            //     'frontEnd' => false,
            //     'backEnd' => true,
            //     "bodyClass" => "",
            //     "wrapperClass" => "",
            //     "class" => "",
            //     "reqLogin" => true,
            //     "allowTo" => array('admin'),
            //     "public" => true,
            //     "hasCategories" => false,
            //     "showNavbar" => true,
            //     "showFooter" => true,
            //     "sidebar" => false,
            //     "showOnMenu" => true,
            //     "showOnFooter" => false,
            //     "icon" => "bi bi-bag",
            //     "icon_color" => ""
            // ],
            'newsletter' => [
                'title' => 'Newsletter',
                'home_page' => false,
                'template' => 'newsletter.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-send",
                "icon_color" => ""
            ],
            'therapists' => [
                'title' => 'Διαχείριση Θεραπευτών',
                'template' => 'therapists.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "fa fa-person-running",
                "icon_color" => ""
            ],
            'clients' => [
                'title' => 'Πελατολόγιο',
                'template' => 'clients.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-person-lines-fill",
                "icon_color" => ""
            ],
            'payments' => [
                'title' => 'Πληρωμές',
                'home_page' => false,
                'template' => 'payments.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-credit-card",
                "icon_color" => ""
            ],
            'settings' => [
                'title' => 'Ρυθμίσεις',
                'home_page' => false,
                'template' => 'settings.php',
                'frontEnd' => false,
                'backEnd' => true,
                "bodyClass" => "",
                "wrapperClass" => "",
                "class" => "",
                "reqLogin" => true,
                "allowTo" => array('admin'),
                "public" => true,
                "hasCategories" => false,
                "showNavbar" => true,
                "showFooter" => true,
                "sidebar" => false,
                "showOnMenu" => true,
                "showOnFooter" => false,
                "icon" => "bi bi-gear",
                "icon_color" => ""
            ],
        ];
    }


    function addSubpage($pages, $parentKey, $newSubpageKey, $newSubpageData)
    {
        foreach ($pages as $key => &$page) {
            if ($key === $parentKey) {
                $page['subpages'][$newSubpageKey] = $newSubpageData;
                return true;
            }

            if (isset($page['subpages'])) {
                if ($this->addSubpage($page['subpages'], $parentKey, $newSubpageKey, $newSubpageData)) {
                    return true;
                }
            }
        }

        return false;
    }

    // Usage
    // $success = addSubpage($pages, 'history', 'newSubpageKey', [
    //     'title' => 'New Subpage',
    //     // ... (other properties)
    // ]);


    function findPageByKey($pages, $keyToFind)
    {
        foreach ($pages as $key => $page) {
            if ($key === $keyToFind) {
                return $page;
            }

            if (isset($page['subpages'])) {
                $found = $this->findPageByKey($page['subpages'], $keyToFind);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    function displayPages($pages)
    {
        echo '<ul>';

        foreach ($pages as $key => $page) {
            echo '<li>';
            echo $page['title'];

            // Check if there are subpages and display them recursively
            if (isset($page['subpages']) && !empty($page['subpages'])) {
                $this->displayPages($page['subpages']);
            }

            echo '</li>';
        }

        echo '</ul>';
    }

    public function getNestedPage($pageSegments, &$segmentsUsed)
    {
        $currentPages = $this->pages;
        $currentPage = null;
        $segmentsUsed = 0;

        foreach ($pageSegments as $segment) {
            if (isset($currentPages[$segment])) {
                $currentPage = $currentPages[$segment];
                $segmentsUsed++;
                if (isset($currentPage['subpages'])) {
                    $currentPages = $currentPage['subpages'];
                } else {
                    $currentPages = [];
                }
            } else {
                break; // No more matching pages
            }
        }

        return $currentPage;
    }



    public function getPageInfo($pageSegments)
    {
        $segmentsUsed = 0;
        $currentPage = $this->getNestedPage($pageSegments, $segmentsUsed);

        if ($currentPage === null) {
            return null; // Page not found
        }

        return [
            'pageInfo' => $currentPage,
            'segmentsUsed' => $segmentsUsed
        ];
    }

    public function getAllPages()
    {
        return $this->pages;
    }
}
