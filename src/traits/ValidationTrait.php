<?php
namespace RapiExpress\Traits;

trait ValidationTrait {
    protected function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $ruleItems = explode('|', $ruleSet);
            foreach ($ruleItems as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramStr) = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }
                $methodName = 'validate' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $isValid = $this->$methodName($value, ...$params);
                    if (!$isValid) {
                        $errors[$field][] = "Validation failed for rule: {$rule}";
                    }
                }
            }
        }
        return $errors;
    }

    protected function validateRequired($value): bool {
        return !empty(trim($value));
    }

    protected function validateEmail($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateUsername($value): bool {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $value);
    }

    protected function sanitize(array $data): array {
        $sanitizedData = [];
        foreach ($data as $key => $value) {
            $sanitizedData[$key] = htmlspecialchars(stripslashes(trim($value)));
        }
        return $sanitizedData;
    }
}
