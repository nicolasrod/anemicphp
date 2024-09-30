<?php

declare(strict_types=1);

namespace Anemic {
    class Users
    {
        static function GetId(string $email): int
        {
            $id = -1;

            Db::RunInTx(function ($db) use ($email, &$id) {
                $rs = Db::Select($db, "users", ["id"], ["email" => $email]);

                if ($rs !== false) {
                    $id = $rs[0]["id"];
                }
            }, Config::Get("auth_database"));

            return $id;
        }

        static function Add(string $email, string $password, string $firstname, string $lastname): bool
        {
            return Db::RunInTx(function ($db) use ($email, $password, $firstname, $lastname) {
                Db::Insert($db, "users", [
                    "email" => $email,
                    "fistname" => $firstname,
                    "lastname" => $lastname,
                    "password_hash" => password_hash($password, PASSWORD_DEFAULT)
                ]);
            }, Config::Get("auth_database"));
        }

        static function Update(string $email, string $password, string $firstname, string $lastname): bool
        {
            return Db::RunInTx(function ($db) use ($email, $password, $firstname, $lastname) {
                Db::Update($db, "users", [
                    "fistname" => $firstname,
                    "lastname" => $lastname,
                    "password_hash" => password_hash($password, PASSWORD_DEFAULT)
                ], [
                    "email" => $email,
                ]);
            }, Config::Get("auth_database"));
        }

        static function Delete(string $email): bool
        {
            return Db::RunInTx(function ($db) use ($email) {
                Db::Delete($db, "users", [
                    "email" => $email,
                ]);
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
    }
}
