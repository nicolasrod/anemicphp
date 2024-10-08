<?php

declare(strict_types=1);

namespace Anemic {

    use SQLite3;

    class Auth
    {
        static function CheckBasic(string $domain, string $user, string $password): void
        {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header("WWW-Authenticate: Basic realm=\"{$domain}\"");
                Page::ErrorUnauthorized();
            } else {
                if ($_SERVER['PHP_AUTH_USER'] !== $user || $_SERVER['PHP_AUTH_PW'] !== $password) {
                    Page::ErrorUnauthorized();
                }
            }
        }

        static function RequireLoggedInUser(): void
        {
            if (empty($_SESSION[Config::Get("username_session")])) {
                Page::ErrorUnauthorized();
            };
        }

        static function IsUserLogged(): bool
        {
            return ! empty($_SESSION[Config::Get("username_session")]);
        }


        static function RequireRole(string $rolename): void
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                Page::ErrorUnauthorized();
            }

            if (! Users::HasRole($user["id"], $rolename)) {
                Page::ErrorUnauthorized();
            }
        }

        /**
         * Save information about the current user in session
         *
         * @param array<string, mixed> $info id and email of the logged in user
         *
         * @return void
         */
        static function SetUser(array $info): void
        {
            $_SESSION[Config::Get("username_session")] = $info;
        }

        /**
         * Get information about the current user in session
         *
         * @return array<string, mixed> id and email of the current logged in user
         */
        static function GetUser(): array
        {
            return $_SESSION[Config::Get("username_session")] ?? [];
        }

        static function Logout(): void
        {
            unset($_SESSION[Config::Get("username_session")]);
        }
    }
}
