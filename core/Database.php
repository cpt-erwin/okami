<?php

namespace Okami\Core;

use PDO;

/**
 * Class Database
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // If any error occurs throw an exception
    }

    public function applyMigrations()
    {
        $this->createMigrationTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(App::$ROOT_DIR . '/migrations');
        $pendingMigrations = array_diff($files, $appliedMigrations);

        $newMigrations = [];
        foreach ($pendingMigrations as $pendingMigration) {
            if ($pendingMigration === '.' || $pendingMigration === '..') {
                continue;
            }

            require_once App::$ROOT_DIR . '/migrations/' . $pendingMigration;
            $className = pathinfo($pendingMigration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $pendingMigration");
            $instance->up();
            $this->log("Migration $pendingMigration applied");
            $newMigrations[] = $pendingMigration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are already applied");
        }
    }

    public function createMigrationTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations(): array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations;");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN); // Return migration column values as a single dimension array
    }

    public function saveMigrations(array $migrations)
    {
        $values = implode(",", array_map(fn($migration) => "('$migration')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $values;");
        $statement->execute();
    }

    protected function log(string $message) {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}