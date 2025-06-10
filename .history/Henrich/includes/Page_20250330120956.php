<?php
declare(strict_types=1);

class Page {
    private static string $title = '';
    private static string $bodyClass = '';
    private static array $scripts = [];
    private static array $styles = [];  // Add styles array
    private static array $settings = [];
    private static string $currentPage = '';  // Add this line
    private static array $inlineScripts = []; // Add this line
    private static bool $isAdminPage = false;

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

    public static function setAdminPage(bool $isAdmin): void {
        self::$isAdminPage = $isAdmin;
    }

    public static function isAdminPage(): bool {
        return self::$isAdminPage;
    }

    public static function addInlineScript($script): void {
        self::$inlineScripts[] = $script;
    }

    public static function renderInlineScripts(): void {
        if (!empty(self::$inlineScripts)) {
            foreach (self::$inlineScripts as $script) {
                echo "<script>{$script}</script>\n";
            }
        }
    }

    public static function render(string $content): void {
        // Clear any processing flags before rendering
        unset($_SESSION['processing_product']);
        
        $current_page = self::getCurrentPage();
        
        require_once dirname(__DIR__) . '/templates/header.php';
        
        echo '<div class="page-wrapper">';
        
        // Include appropriate sidebar based on page type
        if (self::isAdminPage()) {
            require_once dirname(__DIR__) . '/admin/admin-sidebar.php';
        } else {
            require_once dirname(__DIR__) . '/includes/sidebar.php';
        }
        
        // Content area with navbar
        echo '<div class="content-wrapper">';
        if (!self::isAdminPage()) {
            require_once dirname(__DIR__) . '/includes/navbar.php';
        }
        
        echo '<main class="main-content">'; // Add main content wrapper
        echo $content;
        echo '</main>';
        
        require_once dirname(__DIR__) . '/templates/footer.php';
        
        self::renderInlineScripts(); // Add this line
        
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
