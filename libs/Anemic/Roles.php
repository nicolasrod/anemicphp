<?php

declare(strict_types=1);

namespace Anemic {
    class Roles
    {
        static function Grant(string $id_user, string $rolename): bool
        {
            return Db::RunInTx(function ($db) use ($id_user, $rolename) {
                Db::Insert($db, "roles", [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);
            }, Config::Get("auth_database"));
        }

        static function Revoke(string $id_user, string $rolename): bool
        {
            return Db::RunInTx(function ($db) use ($id_user, $rolename) {
                Db::Delete($db, "roles", [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);
            }, Config::Get("auth_database"));
        }

        static function GrantLogged(string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::Grant($user["id"], $rolename);
        }

        static function RevokeLogged(string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::Revoke($user["id"], $rolename);
        }

        static function HasRoleLogged(string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::HasRole($user["id"], $rolename);
        }

        static function HasRole(string $id_user, string $rolename): bool
        {
            $hasit = false;

            Db::RunInTx(function ($db) use ($id_user, $rolename, &$hasit) {
                $rs = Db::Select($db, "roles", ["id_user"], [
                    "id_user" => $id_user,
                    "name" => $rolename
                ]);

                $hasit = (count($rs) > 0);
            }, Config::Get("auth_database"));

            return $hasit;
        }
    }
}
