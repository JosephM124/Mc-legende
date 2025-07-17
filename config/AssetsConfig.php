<?php
namespace Config;

class AssetsConfig
{
    // Configuration de base
    private static $baseUrl = '';
    private static $assetsPath = '/assets';
    private static $adminltePath = '/adminlte';
    
    // CSS Libraries
    private static $cssLibraries = [
        'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
        'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        'animate' => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
        'fullcalendar' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css',
        'sweetalert2' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'
    ];
    
    // JS Libraries
    private static $jsLibraries = [
        'jquery' => 'https://code.jquery.com/jquery-3.6.0.min.js',
        'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        'sweetalert2' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        'fullcalendar' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js',
        'chartjs' => 'https://cdn.jsdelivr.net/npm/chart.js'
    ];
    
    // Local Assets
    private static $localAssets = [
        'css' => [
            'adminlte' => '/adminlte/dist/css/adminlte.min.css',
            'fontawesome' => '/adminlte/plugins/fontawesome-free/css/all.min.css',
            'assets-adminlte' => '/assets/dist/css/adminlte.min.css',
            'assets-fontawesome' => '/assets/plugins/fontawesome-free/css/all.min.css'
        ],
        'js' => [
            'adminlte' => '/adminlte/dist/js/adminlte.min.js',
            'jquery' => '/adminlte/plugins/jquery/jquery.min.js',
            'bootstrap' => '/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js',
            'assets-adminlte' => '/assets/dist/js/adminlte.min.js',
            'assets-bootstrap' => '/assets/plugins/bootstrap/js/bootstrap.bundle.min.js'
        ]
    ];
    
    /**
     * Définit l'URL de base pour les assets
     */
    public static function setBaseUrl($url)
    {
        self::$baseUrl = rtrim($url, '/');
    }
    
    /**
     * Obtient l'URL de base
     */
    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }
    
    /**
     * Génère un lien CSS
     */
    public static function css($name, $type = 'local')
    {
        if ($type === 'cdn' && isset(self::$cssLibraries[$name])) {
            return self::$cssLibraries[$name];
        }
        
        if ($type === 'local' && isset(self::$localAssets['css'][$name])) {
            return self::$baseUrl . self::$localAssets['css'][$name];
        }
        
        return self::$baseUrl . self::$assetsPath . '/css/' . $name . '.css';
    }
    
    /**
     * Génère un lien JS
     */
    public static function js($name, $type = 'local')
    {
        if ($type === 'cdn' && isset(self::$jsLibraries[$name])) {
            return self::$jsLibraries[$name];
        }
        
        if ($type === 'local' && isset(self::$localAssets['js'][$name])) {
            return self::$baseUrl . self::$localAssets['js'][$name];
        }
        
        return self::$baseUrl . self::$assetsPath . '/js/' . $name . '.js';
    }
    
    /**
     * Génère une balise link pour CSS
     */
    public static function cssTag($name, $type = 'local', $attributes = [])
    {
        $href = self::css($name, $type);
        $attr = '';
        
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<link href="' . $href . '" rel="stylesheet"' . $attr . '>';
    }
    
    /**
     * Génère une balise script pour JS
     */
    public static function jsTag($name, $type = 'local', $attributes = [])
    {
        $src = self::js($name, $type);
        $attr = '';
        
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<script src="' . $src . '"' . $attr . '></script>';
    }
    
    /**
     * Génère plusieurs balises CSS
     */
    public static function cssTags($assets)
    {
        $html = '';
        foreach ($assets as $asset) {
            $name = is_array($asset) ? $asset['name'] : $asset;
            $type = is_array($asset) && isset($asset['type']) ? $asset['type'] : 'local';
            $attributes = is_array($asset) && isset($asset['attributes']) ? $asset['attributes'] : [];
            
            $html .= self::cssTag($name, $type, $attributes) . "\n";
        }
        return $html;
    }
    
    /**
     * Génère plusieurs balises JS
     */
    public static function jsTags($assets)
    {
        $html = '';
        foreach ($assets as $asset) {
            $name = is_array($asset) ? $asset['name'] : $asset;
            $type = is_array($asset) && isset($asset['type']) ? $asset['type'] : 'local';
            $attributes = is_array($asset) && isset($asset['attributes']) ? $asset['attributes'] : [];
            
            $html .= self::jsTag($name, $type, $attributes) . "\n";
        }
        return $html;
    }
    
    /**
     * Assets prédéfinis pour différentes pages
     */
    public static function getPageAssets($page)
    {
        $assets = [
            'default' => [
                'css' => [
                    ['name' => 'bootstrap', 'type' => 'cdn'],
                    ['name' => 'fontawesome', 'type' => 'cdn'],
                    ['name' => 'adminlte', 'type' => 'local']
                ],
                'js' => [
                    ['name' => 'jquery', 'type' => 'local'],
                    ['name' => 'bootstrap', 'type' => 'local'],
                    ['name' => 'adminlte', 'type' => 'local']
                ]
            ],
            'eleve' => [
                'css' => [
                    ['name' => 'assets-fontawesome', 'type' => 'local'],
                    ['name' => 'assets-adminlte', 'type' => 'local'],
                    ['name' => 'fontawesome', 'type' => 'local'],
                    ['name' => 'adminlte', 'type' => 'local'],
                    ['name' => 'animate', 'type' => 'cdn'],
                    ['name' => 'fullcalendar', 'type' => 'cdn']
                ],
                'js' => [
                    ['name' => 'assets-bootstrap', 'type' => 'local'],
                    ['name' => 'assets-adminlte', 'type' => 'local'],
                    ['name' => 'jquery', 'type' => 'local'],
                    ['name' => 'bootstrap', 'type' => 'local'],
                    ['name' => 'adminlte', 'type' => 'local'],
                    ['name' => 'fullcalendar', 'type' => 'cdn']
                ]
            ],
            'admin' => [
                'css' => [
                    ['name' => 'fontawesome', 'type' => 'local'],
                    ['name' => 'adminlte', 'type' => 'local']
                ],
                'js' => [
                    ['name' => 'jquery', 'type' => 'local'],
                    ['name' => 'bootstrap', 'type' => 'local'],
                    ['name' => 'adminlte', 'type' => 'local']
                ]
            ],
            'auth' => [
                'css' => [
                    ['name' => 'bootstrap', 'type' => 'cdn'],
                    ['name' => 'fontawesome', 'type' => 'cdn'],
                    ['name' => 'adminlte', 'type' => 'local']
                ],
                'js' => [
                    ['name' => 'jquery', 'type' => 'local'],
                    ['name' => 'bootstrap', 'type' => 'cdn'],
                    ['name' => 'adminlte', 'type' => 'local']
                ]
            ]
        ];
        
        return isset($assets[$page]) ? $assets[$page] : $assets['default'];
    }
}
?> 