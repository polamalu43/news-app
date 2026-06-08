<?php

namespace Core;

use Core\ValidateRules;

abstract class Request
{
    protected array $errors = [];
    protected array $data   = [];

    public function __construct()
    {
        $this->data = $this->collectRequestData();
    }

    /**
     * HTTPリクエストから全ての入力を収集してマージする
     */
    private function collectRequestData(): array
    {
        $data = array_merge($_GET, $_POST);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // PUT/DELETE/PATCH または JSONリクエストの場合
        if (
            in_array($method, ['PUT', 'DELETE', 'PATCH'], true) ||
            str_contains($contentType, 'application/json')
        ) {
            $rawInput = file_get_contents("php://input");
            $parsedData = json_decode($rawInput, true);

            if (is_array($parsedData)) {
                $data = array_merge($data, $parsedData);
            }
        }

        return $data;
    }

    abstract public function rules(): array;

    public function validate(): bool
    {
        $this->errors = [];
        $validateRules = new ValidateRules();
        $rules = $this->rules();
        $data = $this->data;
        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$ruleName, $param] = $this->parseRule($rule);
                $error = $validateRules->applyRule(
                    $field,
                    $value,
                    $ruleName,
                    $param,
                    $data
                );
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
    public function input(string $field, mixed $default = null): mixed
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
