<?php
namespace Helpers;

use Config\AssetsConfig;

class AssetsHelper
{
    /**
     * Initialise la configuration des assets
     */
    public static function init($baseUrl = null)
    {
        if ($baseUrl === null) {
            $baseUrl = isset($_SERVER['BASE_URI']) ? $_SERVER['BASE_URI'] : '';
        }
        AssetsConfig::setBaseUrl($baseUrl);
    }
    
    /**
     * Génère les balises CSS pour une page
     */
    public static function css($page = 'default')
    {
        $assets = AssetsConfig::getPageAssets($page);
        return AssetsConfig::cssTags($assets['css']);
    }
    
    /**
     * Génère les balises JS pour une page
     */
    public static function js($page = 'default')
    {
        $assets = AssetsConfig::getPageAssets($page);
        return AssetsConfig::jsTags($assets['js']);
    }
    
    /**
     * Génère une balise CSS personnalisée
     */
    public static function cssTag($name, $type = 'local', $attributes = [])
    {
        return AssetsConfig::cssTag($name, $type, $attributes);
    }
    
    /**
     * Génère une balise JS personnalisée
     */
    public static function jsTag($name, $type = 'local', $attributes = [])
    {
        return AssetsConfig::jsTag($name, $type, $attributes);
    }
    
    /**
     * Génère plusieurs balises CSS personnalisées
     */
    public static function cssTags($assets)
    {
        return AssetsConfig::cssTags($assets);
    }
    
    /**
     * Génère plusieurs balises JS personnalisées
     */
    public static function jsTags($assets)
    {
        return AssetsConfig::jsTags($assets);
    }
    
    /**
     * Génère une balise img avec gestion automatique du chemin
     */
    public static function img($src, $alt = '', $attributes = [])
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        $imgSrc = $baseUrl . '/images/' . ltrim($src, '/');
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img src="' . $imgSrc . '" alt="' . htmlspecialchars($alt) . '"' . $attr . '>';
    }
    
    /**
     * Génère une balise link pour une image
     */
    public static function imgSrc($src)
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        return $baseUrl . '/images/' . ltrim($src, '/');
    }
    
    /**
     * Génère une balise link pour un fichier
     */
    public static function file($path)
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        return $baseUrl . '/uploads/' . ltrim($path, '/');
    }
    
    /**
     * Génère une balise audio
     */
    public static function audio($src, $attributes = [])
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        $audioSrc = $baseUrl . '/assets/audio/' . ltrim($src, '/');
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<audio src="' . $audioSrc . '"' . $attr . '></audio>';
    }
    
    /**
     * Génère une balise video
     */
    public static function video($src, $attributes = [])
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        $videoSrc = $baseUrl . '/assets/video/' . ltrim($src, '/');
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<video src="' . $videoSrc . '"' . $attr . '></video>';
    }
    
    /**
     * Génère une balise link pour un favicon
     */
    public static function favicon($type = 'icon')
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        $faviconPath = $baseUrl . '/images/favicon.' . $type;
        
        return '<link rel="icon" type="image/' . $type . '" href="' . $faviconPath . '">';
    }
    
    /**
     * Génère les meta tags pour les réseaux sociaux
     */
    public static function socialMeta($title, $description, $image = null, $url = null)
    {
        $baseUrl = AssetsConfig::getBaseUrl();
        $meta = '';
        
        // Open Graph
        $meta .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
        $meta .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
        if ($image) {
            $meta .= '<meta property="og:image" content="' . $baseUrl . '/images/' . ltrim($image, '/') . '">' . "\n";
        }
        if ($url) {
            $meta .= '<meta property="og:url" content="' . $url . '">' . "\n";
        }
        $meta .= '<meta property="og:type" content="website">' . "\n";
        
        // Twitter Card
        $meta .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $meta .= '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
        $meta .= '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">' . "\n";
        if ($image) {
            $meta .= '<meta name="twitter:image" content="' . $baseUrl . '/images/' . ltrim($image, '/') . '">' . "\n";
        }
        
        return $meta;
    }
    
    /**
     * Génère les meta tags de base
     */
    public static function metaTags($title, $description = '', $keywords = '', $author = 'MC-LEGENDE')
    {
        $meta = '';
        $meta .= '<meta charset="UTF-8">' . "\n";
        $meta .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        $meta .= '<title>' . htmlspecialchars($title) . '</title>' . "\n";
        
        if ($description) {
            $meta .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
        }
        
        if ($keywords) {
            $meta .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
        }
        
        $meta .= '<meta name="author" content="' . htmlspecialchars($author) . '">' . "\n";
        
        return $meta;
    }
}
?> 