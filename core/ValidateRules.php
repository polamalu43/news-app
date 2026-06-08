<?php
namespace Core;

class ValidateRules
{
    public function applyRule(
        string $field,
        mixed $value,
        string $rule,
        ?string $param,
        array $data
    ): ?string
    {
        return match ($rule) {
            'required' => $this->validateRequired($field, $value, $rule),
            'max'      => $this->validateMax($field, $value, $rule, (int)$param),
            'min'      => $this->validateMin($field, $value, $rule, (int)$param),
            'email'    => $this->validateEmail($field, $value, $rule),
            'numeric'  => $this->validateNumeric($field, $value, $rule),
            'unique'   => $this->validateUnique($field, $value, $rule, $param),
            'password' => $this->validatePassword($field, $value, $rule),
            'same_password' => $this->validateSamePassword($field, $value, $rule, $data['password'] ?? null),
            default    => null,
        };
    }

    private function getMessage(
        string $field,
        string $rule,
        ?string $param
    ): string
    {
        $lang = env('APP_LANG', 'ja');
        $messages = require BASE_PATH . '/resources/lang/' . $lang . '/validation.php';
        $message  = $messages[$rule] ?? $messages['default'];

        return str_replace(
            [':field', ":{$rule}"],
            [$field, $param],
            $message
        );
    }

    private function validateRequired(string $field, mixed $value, string $rule): ?string
    {
        if ($value === null || $value === '') {
            return $this->getMessage($field, $rule, null);
        }
        return null;
    }

    private function validateMax(string $field, mixed $value, string $rule, int $max): ?string
    {
        if ($value !== null && mb_strlen($value) > $max) {
            return $this->getMessage($field, $rule, (string)$max);
        }
        return null;
    }

    private function validateMin(string $field, mixed $value, string $rule, int $min): ?string
    {
        if ($value !== null && mb_strlen($value) < $min) {
            return $this->getMessage($field, $rule, (string)$min);
        }
        return null;
    }

    private function validateEmail(string $field, mixed $value, string $rule): ?string
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->getMessage($field, $rule, null);
        }
        return null;
    }

    private function validateNumeric(string $field, mixed $value, string $rule): ?string
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            return $this->getMessage($field, $rule, null);
        }
        return null;
    }

    private function validateUnique(string $field, mixed $value, string $rule, ?string $param): ?string
    {
        if ($value === null || $value === '' || $param === null) {
            return null;
        }

        $parts = explode(',', $param);
        $table = $parts[0];
        $column = $parts[1] ?? $field;

        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?");
        $stmt->execute([$value]);

        if ($stmt->fetchColumn() > 0) {
            return $this->getMessage($field, $rule, null);;
        }
        return null;
    }

    private function validatePassword(string $field, mixed $value, string $rule): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // 半角英数字で、最低8文字以上、英字と数字を最低1つずつ含むという例
        $regex = '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';

        if (!preg_match($regex, (string)$value)) {
            return $this->getMessage($field, $rule, null);
        }
        return null;
    }

    private function validateSamePassword(
        string $field,
        mixed $value,
        string $rule,
        ?string $password
    ): ?string
    {
        if ($value !== $password) {
            return $this->getMessage($field, $rule, null);
        }
        return null;
    }
}
