<?php
declare(strict_types=1);

namespace MyWish\Core;

use MyWish\Config\Env;
use PDO;
use PDOException;

/**
 * Database — PDO wrapper for MySQL with prepared statements
 *
 * Singleton pattern: one connection per request.
 *
 * SECURITY: ALWAYS use prepared statements. NEVER concatenate user input into SQL.
 *
 * Usage:
 *   $db = Database::get();
 *   $user = $db->fetchOne('SELECT * FROM users WHERE id = :id', ['id' => 1]);
 *   $users = $db->fetchAll('SELECT * FROM users WHERE banned = :banned', ['banned' => 0]);
 *   $id = $db->insert('users', ['email' => 'test@test.com', 'name' => 'Test']);
 */
final class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host    = Env::get('DB_HOST', 'localhost');
        $port    = Env::get('DB_PORT', '3306');
        $dbname  = Env::get('DB_NAME');
        $user    = Env::get('DB_USER');
        $pass    = Env::get('DB_PASS', '');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');

        if (!$dbname || !$user) {
            throw new \RuntimeException(
                'Database configuration missing. Check DB_NAME and DB_USER in .env'
            );
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,    // Use real prepared statements
            PDO::ATTR_PERSISTENT         => false,    // Avoid stuck connections
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci",
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Don't leak credentials in error message
            throw new \RuntimeException(
                'Database connection failed. Check DB credentials in .env. Original: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get the singleton instance.
     */
    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Get the underlying PDO instance (for transactions, etc.)
     */
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Run a SELECT and fetch one row. Returns null if no row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Run a SELECT and fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single column value (e.g., COUNT(*), SUM(amount)).
     */
    public function fetchValue(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : null;
    }

    /**
     * Run an INSERT. Returns the new auto-increment ID.
     *
     * Usage:
     *   $id = $db->insert('users', ['email' => 'a@b.com', 'name' => 'Alice']);
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ":{$c}", $columns);

        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Run an UPDATE. Returns number of affected rows.
     *
     * Usage:
     *   $db->update('users', ['name' => 'Alice'], 'id = :id', ['id' => 1]);
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = array_map(fn($c) => "`{$c}` = :{$c}", array_keys($data));

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));

        return $stmt->rowCount();
    }

    /**
     * Run a DELETE. Returns number of affected rows.
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Run an arbitrary statement (for migrations, etc.)
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Rollback a transaction.
     */
    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    // Prevent cloning the singleton
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }
}
