<?php
declare(strict_types=1);

class Page {
    private static string $title = '';
    private static string $bodyClass = '';
    private static array $scripts = [];
    private static array $styles = [];  // Add styles array
    private static array $settings = [];
    private static string $currentPage = '';  // Add this line

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

    // Add this method
    public static function setCurrentPage(string $page): void {
        self::$currentPage = $page;
    }

    // Add this method
    public static function getCurrentPage(): string {
        if (empty(self::$currentPage)) {
            self::$currentPage = basename($_SERVER['PHP_SELF'], '.php');
        }
        return self::$currentPage;
    }

    public static function render(string $content): void {
        // Clear any processing flags before rendering
        unset($_SESSION['processing_product']);
        
        $current_page = self::getCurrentPage();
        
        require_once dirname(__DIR__) . '/templates/header.php';
        
        echo '<div class="page-wrapper">';
        
        // Simplify sidebar inclusion
        require_once dirname(__DIR__) . '/includes/sidebar.php';
        
        // Content area with navbar
        echo '<div class="content-wrapper">';
        require_once dirname(__DIR__) . '/includes/navbar.php';
        
        echo '<main class="main-content">'; // Add main content wrapper
        echo $content;
        echo '</main>';
        
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
