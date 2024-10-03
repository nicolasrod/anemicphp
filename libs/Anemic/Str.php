<?php

declare(strict_types=1);

// TODO: Implement mb_strrchr and mb_strrichr
// TODO: Implement mb_strripos and mb_strrpos

namespace Anemic {
  class Str
  {
    static string $charset = "UTF-8";

    static function SetCharset(string $charset): void
    {
      static::$charset = $charset;
    }

    static function HasPrefix(string $v, string $prefix): bool
    {
      return Str::SubStr($v, 0, Str::Len($prefix)) === $prefix;
    }

    static function HasPostfix(string $v, string $postfix): bool
    {
      return Str::SubStr($v, -Str::Len($postfix)) === $postfix;
    }

    static function RemovePrefix(string $v, string $postfix): string
    {
      return Str::SubStr($v, -Str::Len($postfix));
    }

    static function Len(string $v): int
    {
      return mb_strlen($v, static::$charset);
    }

    static function ToUpper(string $v): string
    {
      return mb_strtoupper($v, static::$charset);
    }

    static function ToLower(string $v): string
    {
      return mb_strtolower($v, static::$charset);
    }

    static function PadRight(string $v, int $len, string $c = " "): string
    {
      return mb_str_pad($v, $len, $c, STR_PAD_RIGHT, static::$charset);
    }

    static function PadLeft(string $v, int $len, string $c = " "): string
    {
      return mb_str_pad($v, $len, $c, STR_PAD_RIGHT, static::$charset);
    }

    static function Pad(string $v, int $len, string $c = " "): string
    {
      return mb_str_pad($v, $len, $c, STR_PAD_BOTH, static::$charset);
    }

    /**
     * @param string $v 
     * @param int<1, max> $no_items
     * @return array<string>
     */
    static function ToArray(string $v, int $no_items = 1): array
    {
      return mb_str_split($v, $no_items, static::$charset);
    }

    static function Cut(string $v, int $start, int|null $end = null): string
    {
      return mb_strcut($v, $start, $end, static::$charset);
    }

    static function SubStr(string $v, int $start, int|null $len = null): string
    {
      return mb_substr($v, $start, $len, static::$charset);
    }

    static function Find(string $v, string $it, int $start = 0, bool $ci = false): int
    {
      if ($ci) {
        return mb_stripos($v, $it, $start, static::$charset) ?: -1;
      } else {
        return mb_strpos($v, $it, $start, static::$charset) ?: -1;
      }
    }

    static function CountMatches(string $v, string $it): int
    {
      return mb_substr_count($v, $it, static::$charset);
    }

    static function Truncate(string $v, int $start, int $width, string $ending = ""): string
    {
      return mb_strimwidth($v, $start, $width, $ending,      static::$charset);
    }

    static function Replace(string $search, string $replace, string $v, bool $ci = false): string
    {
      if (! $ci) {
        return str_replace($search, $replace, $v);
      } else {
        return str_ireplace($search, $replace, $v);
      }
    }

    static function Trim(string $v): string
    {
      return trim($v);
    }

    static function GetTo(string $v, string $it): string
    {
      $r = mb_strstr($v, $it, true, static::$charset) ?: "";

      return ($r == false) ? $v : $r;
    }

    static function GetFrom(string $v, string $it): string
    {
      $r = mb_strstr($v, $it, false, static::$charset) ?: "";
      return ($r == false) ? $v : $r;
    }
  }
}