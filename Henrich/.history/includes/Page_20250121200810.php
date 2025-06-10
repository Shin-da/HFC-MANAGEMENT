<?php
declare(strict_types=1);

class Page {
    private static string $title = '';
    private static string $bodyClass = '';
    private static array $scripts = [];
    private static array $styles = [];  // Add styles array
    private static array $settings = [];

    public static function setTitle(string $title): void {
        self::$title = $title;
    }

    public static function setBodyClass(string $bodyClass): void {
        self::$bodyClass = $bodyClass;
    }

    public static function addScript(string $scriptPath): void {
        self::$scripts[] = $scriptPath;
    }

    public static function addStyle(string $stylePath): void {
        self::$styles[] = $stylePath;
    }

    public static function set(string $key, mixed $value): void {
        self::$settings[$key] = $value;
    }

    public static function render(string $content): void {
        require_once dirname(__DIR__) . '/templates/header.php';
        
        echo '<div class="page-wrapper">';
        
        // Include sidebar
        require_once __DIR__ . '/sidebar.php';
        
        // Content area with navbar
        echo '<div class="content-wrapper">';
        
        // Navbar should be first inside content-wrapper
        require_once __DIR__ . '/navbar.php';
        
        echo '<div class="page-container">';
        echo $content;
        echo '</div>';
        
        require_once dirname(__DIR__) . '/templates/footer.php';
        
        echo '</div>'; // End content-wrapper
        echo '</div>'; // End page-wrapper
    }

    public static function getTitle(): string {
        return self::$title;
    }

    public static function getBodyClass(): string {
        return self::$bodyClass;
    }

    public static function getScripts(): array {
        return self::$scripts;
    }

    public static function getStyles(): array {
        return self::$styles;
    }

    public static function get(string $key): mixed {
        return self::$settings[$key] ?? null;
    }
}

// Add this to the render method or where you initialize your page scripts
Page::addScript('/assets/js/sidebar-dropdown.js');
