<?php
require_once("groups.trait.php"); // Подключаем группы
require_once("sqlinjection.trait.php"); // подключаем файл с трейтом
class Users
{
    use SqlInjectionChecker, add_user_to_group; // Используем трейт в классе

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Метод для добавления нового пользователя
    public function addUser(string $name, string $email, array $group_ids = [])
    {
        // Проверяем значения на sql инъекции
        if ($this->hasSqlInjection($name) || $this->hasSqlInjection($email)) {
            throw new Exception("The data contains invalid characters.");
        }

        // Проверка уникальности email
        if ($this->isEmailExists($email)) {
            throw new Exception("Email '$email' already exist");
        }

        $sql = "INSERT INTO users (name, email, created_at, updated_at) VALUES (:name, :email, datetime('now'), datetime('now'))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $userId = $this->pdo->lastInsertId();

        if (count($group_ids) > 0) {
            foreach ($group_ids as $group_id) {
                $this->addUserToGroup($userId, $group_id);
            }
        }
    }

    // Метод для получения всех пользователей
    public function getUsers()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Метод для обновления данных пользователя
    public function updateUser($id, $name = null, $email = null)
    {

        if ($name !== null && $this->hasSqlInjection($name)) {
            throw new Exception("The data contains invalid characters.");
        }
        if ($email !== null && $this->hasSqlInjection($email)) {
            throw new Exception("The data contains invalid characters.");
        }


        if ($email !== null && $this->isEmailExists($email, $id)) {
            throw new Exception("Email '$email' already exist");
        }

        $sql = "UPDATE users SET updated_at = datetime('now')";
        if ($name !== null) {
            $sql .= ", name = :name";
        }
        if ($email !== null) {
            $sql .= ", email = :email";
        }
        $sql .= " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        // Связываем параметры
        if ($name !== null) {
            $stmt->bindParam(':name', $name);
        }
        if ($email !== null) {
            $stmt->bindParam(':email', $email);
        }
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }
    // Метод для удаления пользователя
    public function deleteUser($id)
    {

        if ($this->hasSqlInjection($id)) {
            throw new Exception("The data contains invalid characters.");
        }

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Метод для проверки существования email
    private function isEmailExists($email, $userId = null)
    {

        if ($this->hasSqlInjection($email)) {
            throw new Exception("The data contains invalid characters.");
        }

        $sql = "SELECT COUNT(*) FROM users WHERE email = :email" . ($userId ? " AND id != :user_id" : "");
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}