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
        // Include your header template
        require_once '../templates/header.php';
        
        // Output the main content