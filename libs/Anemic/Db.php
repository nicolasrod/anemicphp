<?php

declare(strict_types=1);

namespace Anemic {

    use Exception;
    use SQLite3;
    use SQLite3Result;

    class Db
    {
        static function RunInTx(callable $fn, string $dbname = ""): bool
        {
            if (empty($dbname)) {
                $dbname = Config::Get("app_database");
            }

            $db = new SQLite3($dbname);
            $db->exec(<<<SQL
            PRAGMA busy_timeout=5000;
            PRAGMA foreign_keys=ON;
            PRAGMA journal_mode=WAL;
            PRAGMA synchronous=NORMAL;
            PRAGMA mmap_size=134217728;
            PRAGMA journal_size_limit=67108864;
            PRAGMA cache_size=2000;
            SQL);

            try {
                $db->exec("BEGIN TRANSACTION");

                $cancel = $fn($db);
                if (! $cancel) {
                    throw new Exception();
                }

                $db->exec("COMMIT TRANSACTION");
                $db->close();
            } catch (\Exception $e) {
                $db->exec("ROLLBACK TRANSACTION");
                $db->close();

                return false;
            }

            return true;
        }

        /**
         * @param array<string, mixed> $kv
         */
        static function _renderFields(array $kv, callable $fn, string $join = ","): string
        {
            $out = array_map(function ($it) use ($fn) {
                return $fn($it);
            }, array_keys($kv));

            return join($join, $out);
        }

        /**
         * @param array<string, mixed> $fields
         */
        static function Insert(SQLite3 $db, string $table, array $fields): bool
        {
            $tmp_fields = join(", ", array_keys($fields));
            $tmp_params = Db::_renderFields($fields, fn($x) => ":{$x}");

            $sql = "INSERT INTO {$table} ({$tmp_fields}) VALUES ({$tmp_params});";

            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                return false;
            }

            foreach ($fields as $k => $v) {
                $stmt->bindValue(":{$k}", $v);
            }

            return $stmt->execute() !== false;
        }

        /**
         * @param array<string, mixed> $fields
         * @param array<string, mixed> $id_expr
         */
        static function Update(SQLite3 $db, string $table, array $fields, array $id_expr): bool
        {
            $tmp_fields = Db::_renderFields($fields, fn($k) => "{$k} = :$k");
            $tmp_ids = Db::_renderFields($id_expr, fn($k) => "{$k} = :$k", " AND ");

            $sql = "UPDATE {$table} SET {$tmp_fields} WHERE {$tmp_ids}";
            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                return false;
            }

            foreach ($fields as $k => $v) {
                $stmt->bindValue(":{$k}", $v);
            }

            foreach ($id_expr as $k => $v) {
                $stmt->bindValue(":{$k}", $v);
            }

            return $stmt->execute() !== false;
        }

        /**
         * @param array<string, mixed> $id_expr
         */
        static function Delete(SQLite3 $db, string $table, array $id_expr): bool
        {
            $tmp_ids = Db::_renderFields($id_expr, fn($k) => "{$k} = :$k", " AND ");

            $sql = "DELETE FROM {$table} WHERE {$tmp_ids}";
            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                return false;
            }

            foreach ($id_expr as $k => $v) {
                $stmt->bindValue(":{$k}", $v);
            }

            return $stmt->execute() !== false;
        }


        /**
         * @param array<string> $fields
         * @param array<string, mixed> $where
         * @return array<array<string, mixed>>
         */
        static function Select(SQLite3 $db, string $table, array $fields = [], array $where = []): array
        {
            $tmp_fields = join(", ", $fields) ?: "*";
            $tmp_where = Db::_renderFields($where, fn($k) => "{$k} = :$k", " AND ");
            if (!empty($tmp_where)) {
                $tmp_where = " WHERE {$tmp_where}";
            }

            $sql = "SELECT {$tmp_fields} FROM {$table} {$tmp_where}";
            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                return [];
            }

            foreach ($where as $k => $v) {
                $stmt->bindValue(":{$k}", $v);
            }

            return Db::GetRS($stmt->execute());
        }

        /**
         * @return array<array<string, mixed>>
         */
        static function SelectRaw(SQLite3 $db, string $sql): array
        {
            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                return [];
            }

            return Db::GetRS($stmt->execute());
        }

        /**
         * @param SQLite3Result|false $rs
         * @return array<array<string, mixed>>
         */
        static function GetRS($rs): array
        {
            if ($rs === false) {
                return [];
            }

            $data = [];
            while ($row = $rs->fetchArray()) {
                $data[] = $row;
            }

            return $data;
        }
    }
}
