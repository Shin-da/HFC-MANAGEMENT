<?php
declare(strict_types=1);

class Page {
    private static string $title = '';
    private static string $bodyClass = '';
    private static array $scripts = [];
    private static array $styles = [];
    private static array $settings = [];
    private static string $currentPage = '';
    private static array $inlineScripts = [];
    private static bool $isAdminPage = false;
    private static ?string $pageDescription = null;
    private static array $pageVariables = [];

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

    public static function setCurrentPage(string $page): void {
        self::$currentPage = $page;
    }

    public static function getCurrentPage(): string {
        if (empty(self::$currentPage)) {
            self::$currentPage = basename($_SERVER['PHP_SELF'], '.php');
        }
        return self::$currentPage;
    }

    public static function setAdminPage(bool $isAdmin): void {
        self::$isAdminPage = $isAdmin;
        if ($isAdmin) {
            self::addStyle('../assets/css/admin-layout.css');
        }
    }

    public static function isAdminPage(): bool {
        return self::$isAdminPage;
    }

    public static function setPageDescription(string $description): void {
        self::$pageDescription = $description;
    }

    public static function setPageVariable(string $key, mixed $value): void {
        self::$pageVariables[$key] = $value;
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
        
        if (self::isAdminPage()) {
            // Admin page structure
            echo '<div class="admin-layout">';
            require_once dirname(__DIR__) . '/admin/admin-sidebar.php';
            echo '<div class="admin-content">';
            
            // Extract page variables for the template
            extract(self::$pageVariables);
            $pageDescription = self::$pageDescription;
            $mainContent = $content;
            
            // Include admin page template
            require_once dirname(__DIR__) . '/admin/templates/admin-page.php';
            
            echo '</div>'; // End admin-content
            echo '</div>'; // End admin-layout
        } else {
            // Regular page structure
            echo '<div class="page-wrapper">';
            require_once dirname(__DIR__) . '/includes/sidebar.php';
            echo '<div class="content-wrapper">';
            require_once dirname(__DIR__) . '/includes/navbar.php';
            echo '<main class="main-content">';
            echo $content;
            echo '</main>';
            echo '</div>'; // End content-wrapper
            echo '</div>'; // End page-wrapper
        }
        
        require_once dirname(__DIR__) . '/templates/footer.php';
        self::renderInlineScripts();
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
