<?php

namespace Okami\Core\DB;

use Okami\Core\App;
use PDO;
use PDOException;

/**
 * Class Database
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\DB
 */
class Database extends PDO
{
    /**
     * Database constructor.
     *
     * @param array $config
     * @throws PDOException
     */
    public function __construct(array $config)
    {
        parent::__construct($config['dsn'] ?? '', $config['user'] ?? '', $config['password'] ?? '');
        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION); // If any error occurs throw an exception
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
        $this->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations(): array
    {
        $statement = $this->prepare("SELECT migration FROM migrations;");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN); // Return migration column values as a single dimension array
    }

    public function saveMigrations(array $migrations)
    {
        $values = implode(",", array_map(fn($migration) => "('$migration')", $migrations));
        $statement = $this->prepare("INSERT INTO migrations (migration) VALUES $values;");
        $statement->execute();
    }

    protected function log(string $message) {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}