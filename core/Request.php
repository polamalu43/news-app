<?php

namespace Core;

use Core\ValidateRules;

abstract class Request
{
    protected array $errors = [];
    protected array $data   = [];

    public function __construct()
    {
        $data = array_merge($_GET, $_POST);
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (
            $method === 'PUT' ||
            $method === 'DELETE' ||
            $method === 'PATCH' ||
            str_contains($contentType, 'application/json')
        ) {
            $parsedData = json_decode(
                file_get_contents("php://input"),
                true
            );
            if (is_array($parsedData)) {
                $data = array_merge($data, $parsedData);
            }
        }

        $this->data = $data;
    }

    abstract public function rules(): array;

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules() as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$ruleName, $param] = $this->parseRule($rule);
                $validateRules = new ValidateRules();
                $error = $validateRules->applyRule($field, $value, $ruleName, $param);
                if ($error !== null) {
                    $this->errors[$field][] = $error;
                }
            }
        }

        return empty($this->errors);
    }

    /** バリデーション済みデータを取得 */
    public function validated(): array
    {
        return array_intersect_key($this->data, $this->rules());
    }

    /** フィールドの値を取得 */
    public function get(string $field, mixed $default = null): mixed
    {
        return $this->data[$field] ?? $default;
    }

    /** エラーを取得 */
    public function errors(): array
    {
        return $this->errors;
    }

    /** エラーがあるか */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /** 'max:255' → ['max', '255'] に分解 */
    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $param] = explode(':', $rule, 2);
            return [$name, $param];
        }
        return [$rule, null];
    }
}
