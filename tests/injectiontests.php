<?php
$testInputs = [
    ["'; DROP TABLE users; --", "test@example.com"],  // Попытка удалить таблицу
    ["Robert'); DROP TABLE users; --", "test2@example.com"], // Попытка удалить таблицу с закрывающей скобкой
    ["' OR '1'='1", "admin@example.com"],  // Условие всегда истинно
    ["admin' --", "user@example.com"],      // SQL комментарий
    ["user' AND (SELECT 1 FROM users) --", "user@example.com"], // Попытка выполнить подзапрос
];

// Проведение тестов
foreach ($testInputs as $input) {
    $name = $input[0];
    $email = $input[1];

    echo "injection: {$input[0]}        ";

    try {
        // Пробуем добавить пользователя с потенциально вредоносными данными
        $users_control->addUser($name, $email);
        echo "created new user, name = \"$name\", email = \"$email\"\n";
    } catch (Exception $e) {
        echo "error: " . $e->getMessage() . "\n";
    }
}

// Проверка итогов
echo "sql injection tests complited\n";