<?php
$groups_control = new Groups($pdo);//Создаём объект groups

$groups_count = rand(5, 10);// генерируем случайное число групп
for ($i = 0; $i < $groups_count; $i++) {
    $groups_control->addGroup("group" . rand(1, 100));
}

$groups = $groups_control->getAllGroups();//Получаем список и выводим

echo "group list:\n";
print_r($groups);

$group_for_user = $groups[array_rand($groups)];//Выбираем случайного пользователя и группу для добавления
$user_for_group = $users[array_rand($users)];

$groups_control->addUserToGroup($user_for_group['id'], $group_for_user['id']);
echo "user {$user_for_group['name']} added to {$group_for_user['name']}\n";//Добавляем


echo "groups of user {$user_for_group['name']}\n";//Выводим пользователей в группе и группы пользователя дабы убедиться что всё добавилось
print_r($groups_control->getGroupsOfUser($user_for_group['id']));

echo "users in group {$group_for_user['name']}\n";
print_r($groups_control->getUsersInGroup($group_for_user['id']));