<?php

declare(strict_types= 1);

namespace ExpertFramework\Validation;

use ExpertFramework\Database\Database;

/**
 * class Validation
 *
 * @package ExpertFramework\Validation
 * @author jonas-elias
 */
class Validation
{
    /**
     * @var array $data
     */
    private array $data = [];

    /**
     * @var array $rules
     */
    private array $rules;

    /**
     * @var array $errors
     */
    private array $errors = [];

    /**
     * @var array $messages
     */
    private array $messages = [
        'required' => 'O campo :input é obrigatório.',
        'string' => 'O campo :input deve ser do tipo string.',
        'integer' => 'O campo :input deve ser do tipo integer.',
        'float' => 'O campo :input deve ser do tipo float.',
        'min' => 'O campo :input deve conter pelo menos :min caracteres.',
        'max' => 'O campo :input não deve conter mais de :max caracteres.',
        'exists' => 'O campo :input já existe na tabela.',
        'not_exists' => 'O campo :input não existe na tabela.'
    ];

    /**
     * Method to validate rules
     *
     * @param array $data
     * @param array $rules
     * @return void
     */
    public function validate(array $data, array $rules): void
    {
        $this->data = $data;
        $this->rules = $rules;

        foreach ($this->rules as $field => $rule) {
            $rules = explode('|', $rule);
            foreach ($rules as $r) {
                $params = [];
                if (strpos($r, ':') !== false) {
                    list($r, $param) = explode(':', $r);
                    $params = explode(',', $param);
                }

                $method = "validate" . ucfirst($r);
                if (method_exists($this, $method)) {
                    $result = $this->$method($field, $params);

                    if ($result['nullable'] ?? false) {
                        break;
                    }

                    if (($result['error'] ?? false)) {
                        $this->errors[$field][] = str_replace(
                            ':max',
                            $params[0] ?? '',
                            str_replace(
                                ':min',
                                $params[0] ?? '',
                                str_replace(
                                    ':input',
                                    $field,
                                    $this->messages[$result['message']]
                                )
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Method to verify if exists fails
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Method to get errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Method to validate required input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateRequired(string $field, mixed $params): array
    {
        return [
            'error' => !isset($this->data[$field]) || empty($this->data[$field]),
            'message' => 'required'
        ];
    }

    /**
     * Method to validate nullable input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateNullable(string $field, mixed $params): array
    {
        return [
            'nullable' => is_null($this->data[$field] ?? null),
        ];
    }

    /**
     * Method to validate string input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateString(string $field, mixed $params): array
    {
        return [
            'error' => !is_string($this->data[$field] ?? false),
            'message' => 'string'
        ];
    }

    /**
     * Method to validate integer input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateInteger(string $field, mixed $params)
    {
        return [
            'error' => !is_integer($this->data[$field] ?? false),
            'message' => 'integer'
        ];
    }

    /**
     * Method to validate float input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateFloat(string $field, mixed $params)
    {
        return [
            'error' => !is_float($this->data[$field] ?? false),
            'message' => 'float'
        ];
    }

    /**
     * Method to validate min input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateMin(string $field, mixed $params): array
    {
        $error = false;
        if (strlen(($this->data[$field] ?? '')) < $params[0]) {
            $error = true;
        }

        return [
            'error' => $error,
            'message' => 'min'
        ];
    }

    /**
     * Method to validate max input
     *
     * @param string $field
     * @param mixed $params
     * @return array
     */
    private function validateMax(string $field, mixed $params): array
    {
        $error = false;
        if (strlen(($this->data[$field] ?? '')) > $params[0]) {
            $error = true;
        }

        return [
            'error' => $error,
            'message' => 'max'
        ];
    }

    /**
     * Method to validate exists item
     *
     * @param string $field
     * @param array $params
     * @return array
     */
    private function validateExists(string $field, array ...$params): array
    {
        $table = $params[0][0];
        $column = $params[0][1];
        $value = $this->data[$field];

        $error = isset(Database::table($table)->where($column, '=', $value)->get()[0]);

        return [
            'error' => $error,
            'message' => 'exists'
        ];
    }

    /**
     * Method to validate exists item
     *
     * @param string $field
     * @param array $params
     * @return array
     */
    private function validateNotExists(string $field, array ...$params): array
    {
        $table = $params[0][0];
        $column = $params[0][1];
        $value = $this->data[$field] ?? null;

        if ($value) {
            $error = !isset(Database::table($table)->where($column, '=', $value)->get()[0]);
        }

        return [
            'error' => $error ?? true,
            'message' => 'not_exists'
        ];
    }
}
