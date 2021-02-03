<?php

namespace Okami\Core\Models;

use Exception;
use Okami\Core\App;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Model
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class Model
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * @var string|null
     */
    private static ?string $tableName = null;

    /**
     * @var string
     */
    private static string $primaryKey = 'id';

    /**
     * @var string|null
     */
    private static ?string $defaultSortColumn = null;

    /**
     * @var string
     */
    private static string $defaultSortMode = self::SORT_ASC;

    /**
     * @param mixed $value
     *
     * @return static
     * @throws Exception
     */
    public static function find($value): Model
    {
        try {
            $query = App::$app->db->prepare("SELECT * FROM `" . static::getTableName() . "` WHERE `" . static::$primaryKey . "` = '{$value}';");
            $query->execute();
            $result = $query->fetchObject(static::class);
            if ($result === false) {
                throw new Exception("Method fetch() has returned false!");
            }

            return $result;
        } catch (PDOException $e) {
            throw new Exception("An error has occurred while reading DB table " . static::getTableName() . ": {$e->getMessage()}");
        }
    }

    /**
     * @return string
     */
    protected static function getTableName(): string
    {
        // FIXME: Pass static::class to some method that will return the plural of that word
        return is_null(static::$tableName) ? static::class : static::$tableName;
    }

    /**
     * @param array $where e.g. ['email' => 'email@example.com', 'status' => 1]
     *
     * @return static
     * @throws PDOException|Exception
     */
    public function findOne(array $where): Model
    {
        $tableName = static::getTableName();
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attribute) => "`$tableName`.`$attribute` = :$attribute", $attributes));
        $query = self::prepare("SELECT * FROM {$tableName} WHERE {$sql};");

        foreach ($where as $key => $value) {
            $query->bindValue(":$key", $value);
        }

        $query->execute();

        $result = $query->fetchObject(static::class);
        if ($result === false) {
            throw new Exception("Method fetch() has returned false!");
        }

        return $result;
    }

    /**
     * @param string $SQL
     *
     * @return PDOStatement
     * @throws PDOException
     */
    private static function prepare(string $SQL): PDOStatement
    {
        return App::$app->db->prepare($SQL);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $tableName = static::getTableName();
        $fillables = $this->fillables();
        $params = array_map(fn($attribute) => ":$attribute", $fillables);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $fillables) . ") 
            VALUES (" . implode(',', $params) . ");");

        foreach ($fillables as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        return $statement->execute();
    }

    /**
     * Specifies which table columns can be manipulated with
     *
     * @return array
     */
    abstract protected function fillables(): array;

    /**
     * @param string $modelClassName
     * @param string $foreignKey
     * @param $value
     *
     * @return static[]
     * @throws Exception
     */
    protected function hasMany(string $modelClassName, string $foreignKey, $value): array
    {
        $model = $this->getModelFromClassName($modelClassName);

        return $model::getAll("`{$foreignKey}` = '{$value}'");
    }

    /**
     * @param string $modelClassName
     *
     * @return static
     * @throws Exception
     */
    private function getModelFromClassName(string $modelClassName): Model
    {
        $model = new $modelClassName();
        if (!$model instanceof Model) {
            throw new Exception("Class {$modelClassName} must be a child of class Model!");
        }

        return $model;
    }

    /**
     * @param string|null $where
     *
     * @return static[]
     * @throws Exception
     */
    public static function getAll(?string $where = null): array
    {
        try {
            $sql = "SELECT * FROM `" . static::getTableName() . "`" . (is_null($where) ? "" : " WHERE {$where}");
            $sql .= " ORDER BY `" . static::getDefaultSortColumn() . "` " . static::$defaultSortMode . ";";
            $query = self::prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, static::class);
        } catch (PDOException $e) {
            throw new Exception("An error has occurred while reading DB table " . static::getTableName() . ": {$e->getMessage()}");
        }
    }

    /**
     * @return string
     */
    protected static function getDefaultSortColumn(): string
    {
        return is_null(static::$defaultSortColumn) ? static::getPrimaryKey() : static::$defaultSortColumn;
    }

    /**
     * @return string
     */
    protected static function getPrimaryKey(): string
    {
        return self::$primaryKey;
    }

    /**
     * Connecting two tables via junction table
     *
     * @param string $modelClassName Desired table class to connect with
     * @param string $junctionClassName Junction table class
     * @param string $junctionLocalKey Junction table primary key of the table class calling this method on
     * @param string $junctionForeignKey Junction table primary key of desired table
     * @param string $foreignKey Foreign key of desired table
     * @param mixed $value Primary key value of the table class calling this method on
     *
     * @return Model[]
     * @throws Exception
     */
    protected function hasManyThrough(
        string $modelClassName,
        string $junctionClassName,
        string $junctionLocalKey,
        string $junctionForeignKey,
        string $foreignKey,
        $value
    ): array {
        $model = $this->getModelFromClassName($modelClassName);
        $junction = $this->getModelFromClassName($junctionClassName);

        try {
            $sql = "SELECT `{$model::getTableName()}`.*, `{$this::getTableName()}`.`{$this::$primaryKey}` as `{$this::$primaryKey}` FROM `{$model::getTableName()}`
                JOIN `{$junction::getTableName()}` ON `{$junction::getTableName()}`.`{$junctionLocalKey}` = `{$model::getTableName()}`.`{$junctionForeignKey}`
                JOIN `" . $this::getTableName() . "` ON `{$this::getTableName()}`.`{$this::$primaryKey}` = `{$junction::getTableName()}`.`{$foreignKey}`
                WHERE `{$this::getTableName()}`.`{$this::$primaryKey}` = {$value}
                ORDER BY `" . $model::getDefaultSortColumn() . "` " . $model::$defaultSortMode;
            $query = self::prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, $modelClassName);
        } catch (PDOException $e) {
            throw new Exception("An error has occurred while reading DB table " . static::getTableName() . ": {$e->getMessage()}");
        }
    }
}