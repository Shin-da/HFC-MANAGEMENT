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
        // Fix template paths to use absolute paths
        require_once dirname(__DIR__) . '/templates/header.php';
        echo $content;
        require_once dirname(__DIR__) . '/templates/footer.php';
    }

    public static function getTitle(): string {
        return self::$title;
        // Include your footer template
        require_once $templatePath . 'footer.php';
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
