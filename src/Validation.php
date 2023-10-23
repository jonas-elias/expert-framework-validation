<?php

namespace ExpertFramework\Validation;

/**
 * class Validation
 *
 * @package ExpertFramework\Validation
 * @author jonas-elias
 */
class Validation
{
    private $data;

    private $rules;

    private $errors = [];

    private array $messages = [
        'required' => 'O campo :input é obrigatório.',
        'string' => 'O campo :input deve ser do tipo string.',
        'integer' => 'O campo :input deve ser do tipo integer.',
        'min' => 'O campo :input deve conter pelo menos :min caracteres.',
        'max' => 'O campo :input não deve conter mais de :max caracteres.',
    ];

    public function __construct($data, $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate()
    {
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

    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }

    private function validateRequired($field, $params)
    {
        return [
            'error' => !isset($this->data[$field]) || empty($this->data[$field]),
            'message' => 'required'
        ];
    }

    private function validateNullable($field, $params)
    {
        return [
            'nullable' => is_null($this->data[$field] ?? null),
        ];
    }

    private function validateString($field, $params)
    {
        return [
            'error' => !is_string($this->data[$field] ?? false),
            'message' => 'string'
        ];
    }

    private function validateInteger($field, $params)
    {
        return [
            'error' => !is_integer($this->data[$field] ?? false),
            'message' => 'integer'
        ];
    }

    private function validateMin($field, $params)
    {
        $error = false;
        if (strlen(($this->data[$field] ?? null)) < $params[0]) {
            $error = true;
        }

        return [
            'error' => $error,
            'message' => 'min'
        ];
    }

    private function validateMax($field, $params)
    {
        $error = false;
        if (strlen(($this->data[$field] ?? null)) > $params[0]) {
            $error = true;
        }

        return [
            'error' => $error,
            'message' => 'max'
        ];
    }

    private function validateExists(...$args)
    {
        $table = $args[0];
        $column = $args[1];
        $value = $args[2];

        var_dump($args);
    }
}
