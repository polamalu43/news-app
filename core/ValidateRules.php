<?php
namespace Core;

class ValidateRules
{
    public function applyRule(
        string $field,
        mixed $value,
        string $rule,
        ?string $param
    ): ?string
    {
        return match ($rule) {
            'required' => $this->validateRequired($field, $value),
            'max'      => $this->validateMax($field, $value, (int)$param),
            'min'      => $this->validateMin($field, $value, (int)$param),
            'email'    => $this->validateEmail($field, $value),
            'numeric'  => $this->validateNumeric($field, $value),
            'unique'   => $this->validateUnique($field, $value, $param),
            default    => null,
        };
    }

    private function getMessage(string $field, string $rule, ?string $param): string
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

    private function validateRequired(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return $this->getMessage($field, 'required', null);
        }
        return null;
    }

    private function validateMax(string $field, mixed $value, int $max): ?string
    {
        if ($value !== null && mb_strlen($value) > $max) {
            return $this->getMessage($field, 'max', (string)$max);
        }
        return null;
    }

    private function validateMin(string $field, mixed $value, int $min): ?string
    {
        if ($value !== null && mb_strlen($value) < $min) {
            return $this->getMessage($field, 'min', (string)$min);
        }
        return null;
    }

    private function validateEmail(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->getMessage($field, 'email', null);
        }
        return null;
    }

    private function validateNumeric(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            return $this->getMessage($field, 'numeric', null);
        }
        return null;
    }

    private function validateUnique(string $field, mixed $value, ?string $param): ?string
    {
        if ($value === null || $value === '' || $param === null) {
            return null;
        }

        [$table, $column] = explode(',', $param);
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$value]);

        if ($stmt->fetchColumn() > 0) {
            return "{$field} はすでに使用されています。";
        }
        return null;
    }

    private function validatePassword(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // 半角英数字で、最低8文字以上、英字と数字を最低1つずつ含むという例
        $regex = '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';

        if (!preg_match($regex, (string)$value)) {
            return "{$field} は8文字以上で、英字と数字をそれぞれ1文字以上含める必要があります。";
        }
        return null;
    }

    private function validateSamePassword(string $field, mixed $value, ?string $otherField, array $allData): ?string
    {
        if ($value !== ($allData[$otherField] ?? null)) {
            return "{$field} が一致しません。";
        }
        return null;
    }
}
