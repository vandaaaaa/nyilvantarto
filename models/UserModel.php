<?php

class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByVerifyToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE verify_token = :token AND verify_token_expires > NOW()"
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(
        string $name,
        string $email,
        string $password,
        string $role,
        ?int $studentId = null,
        bool $verified = false,
        ?string $token = null,
        ?string $tokenExpires = null
    ): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role, student_id, email_verified, verify_token, verify_token_expires)
            VALUES (:name, :email, :password, :role, :student_id, :verified, :token, :token_expires)
        ");
        return $stmt->execute([
            'name'          => $name,
            'email'         => $email,
            'password'      => $hash,
            'role'          => $role,
            'student_id'    => $studentId,
            'verified'      => $verified ? 1 : 0,
            'token'         => $token,
            'token_expires' => $tokenExpires,
        ]);
    }

    public function verifyEmail(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET email_verified = 1, verify_token = NULL, verify_token_expires = NULL WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    public function getAll(): array
    {
        return $this->pdo->query("
            SELECT u.*, s.name as student_name
            FROM users u
            LEFT JOIN students s ON u.student_id = s.id
            ORDER BY u.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function updateRole(int $id, string $role): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->execute(['id' => $id, 'role' => $role]);
    }

    public function updateProfile(int $id, string $name, ?string $password = null): void
    {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET name = :name, password = :password WHERE id = :id");
            $stmt->execute(['name' => $name, 'password' => $hash, 'id' => $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
            $stmt->execute(['name' => $name, 'id' => $id]);
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getLastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }
}
