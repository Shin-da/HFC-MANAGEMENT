<?php
declare(strict_types=1);

class Page {
    private static string $title = '';
    private static string $bodyClass = '';
    private static array $scripts = [];
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

    public static function set(string $key, mixed $value): void {
        self::$settings[$key] = $value;
    }

    public static function render(string $content): void {
        // Include the base header
        require_once dirname(__DIR__) . '/templates/header.php';
        
        // Include existing navbar and sidebar from includes directory
        require_once __DIR__ . '/navbar.php';
        require_once __DIR__ . '/sidebar.php';
        
        // Main content wrapper
        echo '<main class="main-content">';
        echo $content;
        echo '</main>';
        
        // Include footer
        require_once dirname(__DIR__) . '/templates/footer.php';
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

    public static function get(string $key): mixed {
        return self::$settings[$key] ?? null;
    }
}
