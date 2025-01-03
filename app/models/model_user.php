<?php
class Model_User extends Model
{
    public function checkUsersTableExists()
    {
        // Проверяем, существует ли таблица images
        $checkTable = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users';");
        if ($checkTable->fetchColumn() === false) {
            return false;
        } else {
            return true;
        }
    }

    // Проверяет, есть ли пользователь с таким login
    public function checkUserExists($login)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM users WHERE login = :login");
        $query->bindParam(':login', $login, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn() > 0;
    }

    // Регистрирует нового пользователя
    public function registerUser($login, $name, $password, $role = 'user')
    {
        // Проверяем наличие таблицы
        $usersTableExists = $this->checkUsersTableExists();

        if ($usersTableExists) {
            // Хэшируем пароль
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $query = $this->db->prepare("INSERT INTO users (login, name, password, role, created_at) VALUES (:login, :name, :password, :role, datetime('now', 'localtime'))");
            $query->bindParam(':login', $login, PDO::PARAM_STR);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->bindParam(':role', $role, PDO::PARAM_STR); // <-- Новая привязка role
            $query->execute();

            // Возвращаем ID последней вставленной записи
            return $this->db->lastInsertId();
        }
    }

    public function loginUser($login, $password)
    {
        // Находим пользователя по login
        $query = $this->db->prepare("SELECT * FROM users WHERE login = :login");
        $query->bindParam(':login', $login, PDO::PARAM_STR);
        $query->execute();

        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь с таким логином не найден'];
        }

        // Проверяем пароль
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Неверный пароль'];
        }

        // Успешный вход
        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'login' => $user['login'],
                'role' => $user['role'], // <-- Возвращаем роль
            ]
        ];
    }

    /**
     * Обёртка для получения пользователя из БД по login (просто пример)
     */
    public function getUserByLogin($login)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = :login LIMIT 1");
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * То же самое, но по ID
     */
    public function getUserById($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
