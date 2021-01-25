<?php

namespace Okami\Core;

/**
 * Class Model
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public array $errors = [];

    /**
     * @param array $data
     */
    public function loadData(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return mixed
     */
    abstract public function rules(): array;

    /**
     * @return array
     */
    public function labels(): array
    {
        return [];
    }

    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($rule)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $classMame = $rule['class'];
                    $uniqueAttribute = $rule['attribute'] ?? $attribute;
                    $tableName = $classMame::tableName();
                    $statement = App::$app->db->pdo->prepare("SELECT * FROM $tableName WHERE $uniqueAttribute = :attribute;");
                    $statement->bindValue(":attribute", $value);
                    $statement->execute();
                    $record = $statement->fetch();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * @param string $attribute
     * @param string $rule
     * @param array $params
     */
    public function addErrorForRule(string $attribute, string $rule, array $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * @param string $attribute
     * @param string $message
     */
    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * @return string[]
     */
    public function errorMessages(): array {
        return [
            self::RULE_REQUIRED => 'This filed is required',
            self::RULE_EMAIL => 'This filed must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasError(string $attribute): bool
    {
        return array_key_exists($attribute, $this->errors);
    }

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getFirstError(string $attribute): string
    {
        $errors = $this->errors[$attribute] ?? [];
        return $errors[0] ?? '';
    }
}