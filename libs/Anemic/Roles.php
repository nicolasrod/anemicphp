<?php

declare(strict_types=1);

namespace Anemic {

    use SQLite3;

    class Roles
    {
        static function Grant(SQLite3 $db, string $email, string $rolename): bool
        {
            $id_user = Users::GetId($db, $email);
            if ($id_user === -1) {
                return false;
            }

            return Db::Insert($db, "roles", [
                "id_user" => $id_user,
                "name" => $rolename
            ]);
        }

        static function GrantId(SQLite3 $db, string $id_user, string $rolename): bool
        {
            return Db::Insert($db, "roles", [
                "id_user" => $id_user,
                "name" => $rolename
            ]);
        }

        static function Revoke(SQLite3 $db, string $email, string $rolename): bool
        {
            $id_user = Users::GetId($db, $email);
            if ($id_user === -1) {
                return false;
            }
            return Db::Delete($db, "roles", [
                "id_user" => $id_user,
                "name" => $rolename
            ]);
        }

        static function RevokeId(SQLite3 $db, string $id_user, string $rolename): bool
        {
            return Db::Delete($db, "roles", [
                "id_user" => $id_user,
                "name" => $rolename
            ]);
        }


        static function GrantLogged(SQLite3 $db, string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::Grant($db, $user["id"], $rolename);
        }

        static function RevokeLogged(SQLite3 $db, string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::Revoke($db, $user["id"], $rolename);
        }

        // runintx
        static function HasRoleLogged(SQLite3 $db, string $rolename): bool
        {
            $user = Auth::GetUser();
            if (empty($user)) {
                return false;
            }

            return Roles::HasRole($db, $user["id"], $rolename);
        }

        static function HasRole(SQLite3 $db, string $id_user, string $rolename): bool
        {
            $rs = Db::Select($db, "roles", ["id_user"], [
                "id_user" => $id_user,
                "name" => $rolename
            ]);

            return (count($rs) > 0);
        }
    }
}
