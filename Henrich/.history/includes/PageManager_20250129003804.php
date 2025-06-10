<?php
class PageManager {
    private static array $allowedPages = [
        'ceo' => [
            'index', 'financial', 'intelligence', 'supply-chain', 
            'hr', 'quality', 'reports', 'compliance', 'settings'
        ]
    ];

    public static function validateAccess(string $role, string $page): bool {
        if (!isset(self::$allowedPages[$role])) {
            return false;
        }

        return in_array($page, self::$allowedPages[$role]);
    }

    public static function redirectToDefault(string $role): void {
        $defaultPages = [
            'ceo' => 'index.php',
            'admin' => 'admin/dashboard.php',
            'supervisor' => 'supervisor/dashboard.php'
        ];

        $defaultPage = $defaultPages[$role] ?? 'login/login.php';
        header("Location: $defaultPage");
        exit();
    }

    public static function getBreadcrumbs(string $page): array {
        $breadcrumbs = ['Home'];
        $pageParts = explode('-', $page);
        
        foreach ($pageParts as $part) {
            $breadcrumbs[] = ucfirst($part);
        }

        return $breadcrumbs;
    }
}
