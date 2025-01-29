<?php
require_once("sqlinjection.trait.php");
require_once("groups.trait.php");
class Groups
{
    protected $pdo;
    use SqlInjectionChecker, add_user_to_group;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Метод для добавления группы
    public function addGroup(string $group_name): void
    {
        if ($this->hasSqlInjection($group_name)) {
            throw new Exception("The data contains invalid characters.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO groups (name) VALUES (:group_name)");
        $stmt->execute(['group_name' => $group_name]);
    }

    // Метод для получения всех групп
    public function getAllGroups(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM groups");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Метод для получения всех пользователей из группы
    public function getUsersInGroup(int $group_id): array
    {
        if (!$this->groupExists($group_id)) {
            throw new Exception("group $group_id not exists");
        }

        $stmt = $this->pdo->prepare("
            SELECT users.*
            FROM users
            JOIN users_groups_linker ON users.id = users_groups_linker.user_id
            WHERE users_groups_linker.group_id = :group_id
        ");
        $stmt->execute(['group_id' => $group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Метод для получения всех групп в которых состоит пользователь
    public function getGroupsOfUser(int $user_id): array
    {
        if (!$this->userExists($user_id)) {
            throw new Exception("user $user_id not exists");
        }

        $stmt = $this->pdo->prepare("
                SELECT groups.*
                FROM groups
                JOIN users_groups_linker ON groups.id = users_groups_linker.group_id
                WHERE users_groups_linker.user_id = :user_id
            ");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}