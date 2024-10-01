<?php

declare(strict_types=1);

namespace Anemic {
    class Validate
    {
        /**
         * Check
         * @param array<string, mixed> &$data
         * @param array<string, string> $fields
         * @param array<string, string> $messages
         * @return array<string, string>
         */
        static function Check(array &$data, array $fields, array $messages = []): array
        {
            $split = function ($str, $separator) {
                return array_map('trim', explode($separator, $str));
            };

            $rule_messages = array_filter($messages, fn($message) => is_string($message));
            $validation_errors = array_merge([
                'required' => 'Please enter the %s',
                'email' => 'The %s is not a valid email address',
                'min' => 'The %s must have at least %s characters',
                'max' => 'The %s must have at most %s characters',
                'range' => 'The %s must have between %d and %d characters',
                'same' => 'The %s must match with %s',
                'alpha' => 'The %s should have only letters and numbers',
            ], $rule_messages);

            $errors = [];

            foreach ($fields as $field => $option) {
                $rules = $split($option, '|');

                foreach ($rules as $rule) {
                    $params = [];

                    if (strpos($rule, ':')) {
                        [$rule_name, $param_str] = $split($rule, ':');
                        $params = $split($param_str, ',');
                    } else {
                        $rule_name = trim($rule);
                    }

                    $fn = "Is" . ucfirst($rule_name);

                    if (is_callable(["Anemic\Validate", $fn])) {
                        $pass = static::$fn($data, $field, ...$params);
                        if (!$pass) {
                            $errors[$field] = sprintf(
                                // @phpstan-ignore-next-line
                                $messages[$field][$rule_name] ?? $validation_errors[$rule_name],
                                $field,
                                ...$params
                            );
                        }
                    }
                }
            }

            return $errors;
        }

        /**
         * Return true if a string is not empty
         * @param array<string, mixed> $data
         * @param string $field
         * @return bool
         */
        static function IsRequired(array $data, string $field): bool
        {
            return isset($data[$field]) && (trim($data[$field]) !== '');
        }

        /**
         * Return true if a string is not empty
         * @param array<string, mixed> $data
         * @param string $field
         * @return bool
         */
        static function IsEncode(array &$data, string $field): bool
        {
            $data[$field] = filter_var($data[$field], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            return true;
        }

        /**
         * Return true if the value is a valid email
         * @param array<string, mixed> $data
         * @param string $field
         * @return bool
         */
        static function IsEmail(array $data, string $field): bool
        {
            if (empty($data[$field])) {
                return true;
            }

            return filter_var($data[$field], FILTER_VALIDATE_EMAIL) !== false;
        }

        /**
         * Return true if a string has at least min length
         * @param array<string, mixed> $data
         * @param string $field
         * @param string $min
         * @return bool
         */
        static function IsMin(array $data, string $field, string $min): bool
        {
            if (!isset($data[$field])) {
                return true;
            }

            return Str::Len($data[$field]) >= (int)$min;
        }

        /**
         * Return true if a string cannot exceed max length
         * @param array<string, mixed> $data
         * @param string $field
         * @param string $max
         * @return bool
         */
        static function IsMax(array $data, string $field, string $max): bool
        {
            if (!isset($data[$field])) {
                return true;
            }

            return Str::Len($data[$field]) <= (int)$max;
        }

        /**
         * @param array<string, mixed> $data
         * @param string $field
         * @param string $min
         * @param string $max
         * @return bool
         */
        static function IsRange(array $data, string $field, string $min, string $max): bool
        {
            if (!isset($data[$field])) {
                return true;
            }

            $len = Str::Len($data[$field]);
            return $len >= (int)$min && $len <= (int)$max;
        }

        /**
         * Return true if a string equals the other
         * @param array<string, mixed> $data
         * @param string $field
         * @param string $other
         * @return bool
         */
        static function IsSame(array $data, string $field, string $other): bool
        {
            if (isset($data[$field], $data[$other])) {
                return $data[$field] === $data[$other];
            }

            if (!isset($data[$field]) && !isset($data[$other])) {
                return true;
            }

            return false;
        }

        /**
         * Return true if a string is alphanumeric
         * @param array<string, mixed> $data
         * @param string $field
         * @return bool
         */
        static function IsAlpha(array $data, string $field): bool
        {
            if (!isset($data[$field])) {
                return true;
            }

            return ctype_alnum($data[$field]);
        }
    }
}
