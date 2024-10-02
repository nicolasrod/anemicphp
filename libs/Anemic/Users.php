<?php

declare(strict_types=1);

namespace Anemic {

    use SQLite3;

    // TODO: Refactor Users and Roles to make it cleaner. It's becoming a mess
    class Users
    {

        static function List(): array
        {
            $data = [];
            Db::RunInTx(function ($db) use (&$data) {
                $data = Db::Select($db, "users");
            }, Config::Get("auth_database"));

            return $data;
        }

        static function GetId(SQLite3 $db, string $email): int
        {
            $rs = Db::Select($db, "users", ["id"], ["email" => $email]);
            if (! empty($rs)) {
                return $rs[0]["id"];
            }

            return -1;
        }

        static function Add(SQLite3 $db, string $email, string $password, string $firstname, string $lastname): bool
        {
            return Db::Insert($db, "users", [
                "email" => $email,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "password_hash" => password_hash($password, PASSWORD_DEFAULT)
            ]);
        }

        static function Update(SQLite3 $db, string $email, string $password, string $firstname, string $lastname): bool
        {
            return Db::Update($db, "users", [
                "firstname" => $firstname,
                "lastname" => $lastname,
                "password_hash" => password_hash($password, PASSWORD_DEFAULT)
            ], [
                "email" => $email,
            ]);
        }

        static function Delete(SQLite3 $db, string $email): bool
        {
            return Db::Delete($db, "users", ["email" => $email]);
        }

        static function Login(string $email, string $password): bool
        {
            $is_ok = false;

            Db::RunInTx(function ($db) use ($email, $password, &$is_ok) {
                $data = Db::Select($db, "users", ["id", "password_hash"], ["email" => $email]);
                if (empty($data)) {
                    return false;
                }

                $hash = $data[0]["password_hash"];
                $id = $data[0]["id"];
                $is_ok = password_verify($password, $hash);

                if ($is_ok) {
                    Auth::SetUser(["id" => $id, "email" => $email]);
                }
            }, Config::Get("auth_database"));

            return $is_ok;
        }

        static function GetRoles(int $id): array
        {
            $data = [];
            Db::RunInTx(function (SQLite3 $db) use ($id, &$data) {
                $data = array_map(function ($it) {
                    return $it["name"];
                }, Db::Select($db, "roles", ["name"], ["id_user" => $id]));
            }, Config::Get("auth_database"));

            return $data;
        }
    }
}
