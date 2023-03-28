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
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} is required.";
                    }
                }
            } elseif ($rule == 'email') {
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} is not a valid email address.";
                    }
                }
            } elseif ($rule == 'accepted') {
                if ($value != 'on' && $value != 'yes' && $value !== true && $value !== 1) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} must be accepted.";
                    }
                }
            } elseif (\str_contains($rule, 'same:')) {
                $otherField = \str_replace('same:', '', $rule);
                $otherValue = $data[$otherField] ?? null;

                if ($value != $otherValue) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} must match {$otherField}.";
                    }
                }
            } elseif (\str_contains($rule, 'min:')) {
                $min = \str_replace('min:', '', $rule);

                if (\strlen($value) < $min) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} must be at least {$min} characters.";
                    }
                }
            } elseif (\str_contains($rule, 'max:')) {
                $max = \str_replace('max:', '', $rule);

                if (\strlen($value) > $max) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} must be at most {$max} characters.";
                    }
                }
            } elseif (\str_contains($rule, 'required_without:')) {
                $otherField = \str_replace('required_without:', '', $rule);

                if (empty($value) && empty($data[$otherField])) {
                    if (isset($messages[$field][$rule])) {
                        $errors[$rule] = $messages[$field][$rule];
                    } else {
                        $errors[$rule] = "{$fieldLabel} is required.";
                    }
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
            $firstErrorItem = \array_values($errors)[0];
            $message = \array_values($firstErrorItem)[0];
        }

        throw new \InvalidArgumentException($message);
    }
}
