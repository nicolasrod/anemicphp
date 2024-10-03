<?php

declare(strict_types=1);

namespace Anemic {

    use SQLite3;

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

        static function Get(int $id): array
        {
            $item = [];
            Db::RunInTx(function (SQLite3 $db) use ($id, &$item) {
                $item = Db::SelectFirst($db, "users", [], ["id" => $id]);
            }, Config::Get("auth_database"));

            return $item;
        }

        static function GetId(string $email): int
        {
            $id = -1;
            Db::RunInTx(function (SQLite3 $db) use ($email, &$id) {
                $id = Db::SelectFirst($db, "users", ["id"], ["email" => $email])["id"] ?? -1;
            }, Config::Get("auth_database"));

            return $id;
        }

        static function Add(string $email, string $password, string $firstname, string $lastname): bool
        {
            return Db::RunInTx(function (SQLite3 $db) use ($email, $password, $firstname, $lastname) {
                Db::Insert($db, "users", [
                    "email" => $email,
                    "firstname" => $firstname,
                    "lastname" => $lastname,
                    "password_hash" => password_hash($password, PASSWORD_DEFAULT)
                ]);
            }, Config::Get("auth_database"));
        }

        static function Update(int $id, string $firstname, string $lastname): bool
        {
            return Db::RunInTx(function (SQLite3 $db) use ($id, $firstname, $lastname) {
                Db::Update($db, "users", [
                    "firstname" => $firstname,
                    "lastname" => $lastname,
                ], [
                    "id" => $id,
                ]);
            }, Config::Get("auth_database"));
        }

        static function Delete(string $email): bool
        {
            return Db::RunInTx(function (SQLite3 $db) use ($email) {
                Db::Delete($db, "users", ["email" => $email]);
            }, Config::Get("auth_database"));
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

        // ===============================================================================
        // Roles
        // ===============================================================================

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

        static function HasRole(int $id_user, string $rolename): bool
        {
            $has_role = false;

            Db::RunInTx(function (SQLite3 $db) use ($id_user, $rolename, &$has_role) {
                $rs = Db::Select($db, "roles", ["id_user"], [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);

                $has_role =  (count($rs) > 0);
            }, Config::Get("auth_database"));

            return $has_role;
        }

        static function GrantRole(int $id_user, string $rolename): bool
        {
            return Db::RunInTx(function (SQLite3 $db) use ($id_user, $rolename) {
                return Db::Insert($db, "roles", [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);
            }, Config::Get("auth_database"));
        }

        static function RevokeRole(int $id_user, string $rolename): bool
        {
            return Db::RunInTx(function (SQLite3 $db) use ($id_user, $rolename) {
                return Db::Delete($db, "roles", [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);
            }, Config::Get("auth_database"));
        }
    }
}
