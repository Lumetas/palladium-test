<?php

require_once 'users.class.php'; // Подключаем класс Users
require_once 'groups.class.php'; // Подключаем класс groups
// Конфигурация базы данных
$dsn = 'sqlite:palladium.sqlite'; // Используем SQLite с файлом 'palladium.sqlite'
$pdo = new PDO($dsn);
$pdo->exec("DELETE FROM groups; DELETE FROM users; DELETE FROM users_groups_linker");//Чистим все таблицы перед тестом



// Создание экземпляра класса Users
$users_control = new Users($pdo);

// Функция для генерации случайного имени
function randomName() {
    return 'User' . rand(1, 100);
}

// Функция для генерации случайного email
function randomEmail() {
    return 'user' . rand(1, 100) . '@example.com';
}

$users_count = rand(5, 10);



// Создание от 5 до 10 пользователей со случайными данными
for ($i = 0; $i < $users_count; $i++) {
    try {
        $users_control->addUser(randomName(), randomEmail());
    } catch (Exception $e) {
        echo "error: " . $e->getMessage() . "\n";
    }
}

// Вывод списка пользователей
echo "user list:\n";
$users = $users_control->getUsers();
print_r($users);

$user_for_edit = $users[array_rand($users)];
do {
    $user_for_delete = $users[array_rand($users)]; 
} while ($user_for_delete === $user_for_edit);



// Обновление данных случайного пользователя
if (!empty($users)) {
    $newName = randomName();
    $newEmail = randomEmail();

    try {
        $users_control->updateUser($user_for_edit['id'], $newName, $newEmail);
        echo "user {$user_for_edit['id']} successfully updated\n";
    } catch (Exception $e) {
        echo "error: " . $e->getMessage() . "\n";
    }
}

// Удаление одного случайного пользователя (не того, что мы обновили)
if (count($users) > 1) {
    try {
        $users_control->deleteUser($user_for_delete['id']);
        echo "user {$user_for_delete['id']} successfully deleted\n";
    } catch (Exception $e) {
        echo "error: " . $e->getMessage() . "\n";
    }
}

// Вывод обновленного списка пользователей
echo "\n\n----------\n\n\n";
$users = $users_control->getUsers();
print_r($users);

include("tests/groups.php");//Подключаем тесты на управление группами и sql инъекции
include("tests/injectiontests.php");

echo "\n\n----------\n\n\n";//Создаём новый массив с списком пользоваелей чтобы убедиться что sql инъекции ему не повредили
$users_new = $users_control->getUsers();
print_r($users);

if ($users === $users_new) {//Выводим и так же проверяем
    echo "users table not changed.\n";
}

// Удаление всех пользователей
try {
    $pdo->exec("DELETE FROM users");
    echo "all users deleted\n";
} catch (Exception $e) {
    echo "error: " . $e->getMessage() . "\n";
}
