<?php

namespace Okami\Core\DB;

use Okami\Core\App;
use Okami\Core\Model;
use PDOException;
use PDOStatement;

/**
 * Class DbModel
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\DB
 */
abstract class DbModel extends Model
{
    abstract public function tableName(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attribute) => ":$attribute", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ") 
            VALUES (" . implode(',', $params) . ");");

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        return $statement->execute();
    }

    /**
     * @param array $where e.g. ['email' => 'email@example.com', 'status' => 1]
     *
     * @return mixed
     * @throws PDOException
     */
    public function findOne(array $where)
    {
        // Can't use self here since it's abstract
        // static will return the child's method implementation value
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attribute) => "$attribute = :$attribute", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    /**
     * @param string $SQL
     *
     * @return PDOStatement
     * @throws PDOException
     */
    public static function prepare(string $SQL): PDOStatement
    {
        return App::$app->db->prepare($SQL);
    }
}