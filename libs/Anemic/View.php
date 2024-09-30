<?php

declare(strict_types=1);

namespace Anemic {

    /**
     * Contents for encoding
     */
    enum EncodeCtx
    {
        case HTML;
        case URL;
        case RAW;
    }

    /**
     * Minimal master layout view implementation
     * */
    class View
    {

        /**
         * Folder of the template to render
         */
        static string $basepath = "";

        /**
         * Define variables for the view
         *
         * @var array<string, string> $vars
         */
        static array $vars = [];

        /**
         * Defined Blocks
         *
         * @var array<string, string> $blocks
         */
        static array $blocks = [];

        /**
         * Current block name
         */
        static string $blockname = "";

        /**
         * Layout name (defined using Extends)
         */
        static string $layout = "";

        /**
         * Define the start of a block
         *
         * @param string $name name of the block
         *
         * @return void
         */
        static public function BeginBlock(string $name): void
        {
            static::$blockname = $name;
            ob_start();
        }

        /**
         * Ends the block definition
         *
         * @return void
         */
        static public function EndBlock(): void
        {
            $content = ob_get_contents() ?: "";
            @ob_end_clean();

            assert(! empty(static::$blockname));

            static::$blocks[static::$blockname] = $content;
        }

        /**
         * Get the contents of a defined block
         *
         * @param string $name name of the block
         * @param string $def default value to return if not found (default = "")
         *
         * @return string
         */
        static public function GetBlock(string $name, string $def = ""): string
        {
            return static::$blocks[$name] ?? $def;
        }

        /**
         * Get the contents of a variable
         *
         * @param string $name name of the variable
         * @param string $def default value to return if not found (default = "")
         * @param EncodeCtx $ctx encoding context for the output (HTML, JS, RAW) (default = HTML)
         *
         * @return string
         */
        static public function GetStr(string $name, string $def = "", EncodeCtx $ctx = EncodeCtx::HTML): string
        {
            return static::Encode(static::$vars[$name] ?? $def, $ctx);
        }

        static public function GetVar(string $name, mixed $def = []): mixed
        {
            return static::$vars[$name] ?? $def;
        }

        /**
         * HTML encode a value
         *
         * @param string $val value to encode
         *
         * @return string
         */
        static public function Encode(string $val, EncodeCtx $ctx): string
        {
            switch ($ctx) {
                case EncodeCtx::HTML:
                    return htmlentities($val, ENT_QUOTES | ENT_IGNORE | ENT_HTML5, "UTF-8", false);
                case EncodeCtx::URL:
                    return urlencode($val);
                default:
                    return $val;
            }
        }

        /**
         * Return if a view variable is empty 
         *
         * @param string $var name of the variable
         *
         * @return bool
         */
        static public function IsEmpty(string $var): bool
        {
            return empty(static::$vars[$var] ?? "");
        }

        /**
         * Set the master layout name that the template extends
         *
         * @param string $layout name of the layout template file. Dot in the filename will be converted to /.
         *
         * @return void
         */
        static public function Extends(string $layout): void
        {
            static::$layout = $layout;
        }

        /**
         * Render a template file and get its output as a result
         *
         * @param string $fname name of the file to render
         *
         * @return string
         */
        static function _renderFile(string $fname): string
        {
            $tpl = str_replace(".", DIRECTORY_SEPARATOR, $fname);
            $path = static::$basepath . DIRECTORY_SEPARATOR . $tpl . ".tpl";

            assert(file_exists($path));

            ob_start();
            require($path);
            $data = ob_get_contents() ?: "";
            @ob_end_clean();

            return $data;
        }

        /**
         * Include a template
         *
         * @param string $fname name of the template to include
         * @param array<string, string> $params key/value pair of variables to use in the template
         *
         * @return string
         */
        static function Include(string $fname, array $params = []): string
        {
            $oldVars = static::$vars;

            static::$vars = array_merge(static::$vars, $params);
            $out = static::_renderFile($fname);
            static::$vars = $oldVars;

            return $out;
        }

        /**
         * Render a template file and get its output either in stdout or as a return value
         *
         * @param string $name name of the file to render
         * @param array<string, string> $params key/value pair of variables to be set in the view
         *
         * @return void
         */
        static public function Render(string $name, array $params = []): void
        {
            static::$vars = array_merge(static::$vars, $params);

            $out = static::_renderFile($name);

            $charset = Config::Get("charset", "UTF-8");
            header("Content-Type: text/html; charset={$charset}");

            if (! empty(static::$layout)) {
                echo static::_renderFile(static::$layout);
                $_SESSION["flashmsg"] = "";
                return;
            }

            $_SESSION["flashmsg"] = "";
            echo $out;
        }

        /**
         * Set base folder to look for views
         * 
         * @param string $name name of the base folder
         * 
         * @return void
         */
        static function SetBaseFolder(string $name): void
        {
            static::$basepath = $name;
        }


        static function FlashMsg(string $msg, bool $success = true): void
        {
            $_SESSION["flashmsg"] = [
                "msg" => $msg,
                "type" => ($success ? "success" : "danger")
            ];
        }

        static function GetFlasgMsg(): mixed
        {
            return $_SESSION["flashmsg"] ?? "";
        }
    }
}
