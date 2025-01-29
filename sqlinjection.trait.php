<?php

trait SqlInjectionChecker {
    
    private function hasSqlInjection(string $input) { //Приватный метод прринимающий строку запроса
        $patterns = [
            '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|FROM|WHERE|UNION|EXEC|EXECUTE|CAST|CONVERT)\b/i',
            '/--/',
            '/;/',
            '/"/',
            "/'/",
            '/\bOR\b/i',
            '/\bAND\b/i',
            '/\bNOT\b/i',
            '/\bNULL\b/i',
            '/\btrue\b/i',
            '/\bfalse\b/i'
        ];

        foreach ($patterns as $pattern) {//Регулярными выражениями проверяем соответствует ли строка хотя бы одному из них
            if (preg_match($pattern, $input)) {
                return true; // Если до возвращаем true
            }
        }
        return false; //В конце возвращаем false если всё хорошо
    }
}