<?php

namespace SebKay\SPV;

class Validator
{
    public static function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;

            $errors[$field] = self::validateField($data, $field, $value, $rule);
        }

        return \array_filter($errors);
    }

    public static function validateField($data, $field, $value, $rules)
    {
        $errors = [];
        $fieldLabel = \ucfirst(\str_replace('_', ' ', $field));

        foreach ($rules as $rule) {
            if (\in_array('nullable', $rules) && empty($value)) {
                continue;
            }

            if ($rule == 'required') {
                if (empty($value)) {
                    $errors[] = "{$fieldLabel} is required.";
                }
            } elseif ($rule == 'email') {
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "{$fieldLabel} is not a valid email address.";
                }
            } elseif ($rule == 'password') {
                if (strlen($value) < 5) {
                    $errors[] = "{$fieldLabel} must be at least 5 characters long.";
                }
            } elseif (\str_contains($rule, 'same')) {
                $otherField = \str_replace('same:', '', $rule);
                $otherValue = $data[$otherField] ?? null;

                if ($value != $otherValue) {
                    $errors[] = "{$fieldLabel} must match {$otherField}.";
                }
            }
        }

        return $errors;
    }

    public static function throwErrors($errors)
    {
        if (empty($errors)) {
            return;
        }

        if ($errors) {
            $message = \array_values($errors)[0][0];
        }

        throw new \InvalidArgumentException($message);
    }
}
