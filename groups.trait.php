<?php
trait add_user_to_group
{
    // Проверяет существует ли группа с данным id
    private function groupExists(int $group_id): bool
    {
        if ($this->hasSqlInjection((string) $group_id)) {
            throw new Exception("The data contains invalid characters.");
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM groups WHERE id = :group_id");
        $stmt->execute(['group_id' => $group_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Проверяет существует ли пользователь с данным id
    private function userExists(int $user_id): bool
    {
        if ($this->hasSqlInjection((string) $user_id)) {
            throw new Exception("The data contains invalid characters.");
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Метод для добавления пользователя в группу
    public function addUserToGroup(int $user_id, int $group_id): void
    {
        if (!$this->userExists($user_id)) {
            throw new Exception("user $user_id not exists");
        }

        if (!$this->groupExists($group_id)) {
            throw new Exception("group $group_id not exists");
        }

        $stmt = $this->pdo->prepare("INSERT INTO users_groups_linker (user_id, group_id) VALUES (:user_id, :group_id)");
        $stmt->execute(['user_id' => $user_id, 'group_id' => $group_id]);
    }
}