<?php

namespace SebKay\SPV;

class Validator
{
    public static function validate(array $data, array $rules, ?array $messages = [])
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;

            $errors[$field] = self::validateField($data, $field, $value, $rule, $messages);
        }

        return \array_filter($errors);
    }

    public static function validateField($data, $field, $value, $rules, ?array $messages = [])
    {
        $errors = [];
        $fieldLabel = \ucfirst(\str_replace('_', ' ', $field));

        foreach ($rules as $rule) {
            if (\in_array('nullable', $rules) && empty($value)) {
                continue;
            }

            if ($rule == 'required') {
                if (empty($value)) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} is required.");
                }
            } elseif (\str_contains($rule, 'required_with:')) {
                $ruleValue = \str_replace('required_with:', '', $rule);
                $otherField = \explode(',', $ruleValue)[0];
                $otherFieldValue = \explode(',', $ruleValue)[1] ?? null;

                if (empty($value) && $data[$otherField] == $otherFieldValue) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} is required with {$otherField} when the value is {$otherFieldValue}.");
                } elseif (empty($value) && ! empty($data[$otherField]) && empty($otherFieldValue)) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} is required with {$otherField}.");
                }
            } elseif (\str_contains($rule, 'required_without:')) {
                $otherField = \str_replace('required_without:', '', $rule);

                if (empty($value) && empty($data[$otherField])) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} is required.");
                }
            } elseif ($rule == 'email') {
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} is not a valid email address.");
                }
            } elseif ($rule == 'accepted') {
                if ($value != 'on' && $value != 'yes' && $value !== true && $value !== 1) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} must be accepted.");
                }
            } elseif (\str_contains($rule, 'same:')) {
                $otherField = \str_replace('same:', '', $rule);
                $otherValue = $data[$otherField] ?? null;

                if ($value != $otherValue) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} must match {$otherField}.");
                }
            } elseif (\str_contains($rule, 'min:')) {
                $min = \str_replace('min:', '', $rule);

                if (\strlen($value) < $min) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} must be at least {$min} characters.");
                }
            } elseif (\str_contains($rule, 'max:')) {
                $max = \str_replace('max:', '', $rule);

                if (\strlen($value) > $max) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, "{$fieldLabel} must be at most {$max} characters.");
                }
            } elseif ($rule == 'strong_password') {
                if (preg_match('/[A-Z]/', $value) == 0 || preg_match('/[a-z]/', $value) == 0 || preg_match('/[0-9]/', $value) == 0) {
                    $errors[$rule] = self::validationMessage($messages, $field, $rule, 'Password must contain at least one uppercase letter, one lowercase letter, and one number.');
                }
            }
        }

        return $errors;
    }

    protected static function validationMessage($messages, $field, $rule, $default)
    {
        return $messages[$field][$rule] ?? $default;
    }

    public static function throwErrors($errors)
    {
        if (empty($errors)) {
            return;
        }

        if ($errors) {
            $firstErrorItem = \array_values($errors)[0];
            $message = \array_values($firstErrorItem)[0];
        }

        throw new \InvalidArgumentException($message);
    }
}
