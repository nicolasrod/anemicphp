<?php

declare(strict_types=1);

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
  }
}

// mb_strstr and mb_stristr
// mb_strrchr and mb_strrichr
// mb_strripos and mb_strrpos

/*
<?php

require_once('StringIterator.php');
require_once('StringException.php');

class String implements IteratorAggregate, ArrayAccess {
  
    const OVERWRITE_FOLLING_CHARACTERS = true;
    const DO_NOT_OVERWRITE_FOLLING_CHARACTERS = false;
    const OFFSET_NORMAL = -1;
    const OFFSET_EXTENDED = 0;
    const DEFAULT_ENCODING = 'UTF-8';
    const LINE_ENDING_N = "\n";
    const LINE_ENDING_RN = "\r\n";
    const LINE_ENDING_R = "\r";
    private $value = null;
    private $encoding = null;
    
    private static $defaultEncoding = self::DEFAULT_ENCODING;
  
  
    public function __construct($value = null, $encoding = null) {
      if(null !== $value) {
        $this->setValue($value);
      }
      
      if(null === $encoding) {
        $encoding = $this->getDefaultEncoding();
      }
      $this->setEncoding($encoding);
    }
    
    public function getValue() {
      return $this->value;
    }
  
    public function setValue($value) {   
      if(is_string($value)) {
        $this->value = $value;
      } elseif($value instanceof String) {
        $this->value = $value->getValue();
      } else {
        throw new StringException('Invalid value given: ' . $value);
      }
  
      return $this;
    }
    
    public function getEncoding() {
      return $this->encoding;
    }
  
    public function setEncoding($encoding) {
      $this->encoding = $encoding;
    }
     
    public function getLength() {
      return mb_strlen($this->getValue(), $this->getEncoding());
    }
  
    public function getIterator() {
      return new StringIterator($this);
    }
    
    public function offsetExists($offset) {
      return $this->offsetExistsExtended($offset, self::OFFSET_NORMAL);
    }
  
    private function offsetExistsExtended($offset, $byteCorrection = self::OFFSET_NORMAL) {
      return ctype_digit("$offset") && $offset >= 0 && ($offset <= $this->getLength() + $byteCorrection);
    }
    
    private function checkOffset($offset, $byteCorrection = self::OFFSET_NORMAL) {
      if(!$this->offsetExistsExtended($offset, $byteCorrection)) {
        throw new StringException('Invalid offset given: ' . $offset);
      }
      return $this;
    }
    
    public function offsetGet($offset) {
      $this->checkOffset($offset);
      $value = $this->getValue();
      return new String(mb_substr($value, $offset, 1, $this->getEncoding()));
    }
  
    public function offsetSet($offset, $char) {
      
      $this->checkOffset($offset, self::OFFSET_EXTENDED);
          
      if(is_string($char)) {
        if(mb_strlen($char, $this->getEncoding()) > 1) {
          throw new StringException('Invalid character given: ' . $char);
        }
      } elseif($char instanceof String) {
        if($char->getLength() > 1) {
          throw new StringException('String contains more than one character: ' . $char);
        }
      } else {
        throw new StringException('Invalid character given: ' . $char);
      }    
      
      $value = $this->getValue();
      $this->insert($char, $offset, self::OVERWRITE_FOLLING_CHARACTERS);
      
      return $this;
    }
  
    public function offsetUnset($offset) {
      $this->checkOffset($offset);
      $value = $this->getValue();
      $prefix = $postfix = '';
      
      if(0 == $offset) {
        $prefix = '';
        $postfix = mb_substr($value, 1, mb_strlen($value) - 1, $this->getEncoding());
      } elseif($this->getLength() - 1 == $offset) {
        $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
        $postfix = '';
      } else {
        $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
        $postfix = mb_substr($value, $offset + 1, $this->getLength() - $offset, $this->getEncoding());
      }
  
      $this->setValue($prefix . $postfix);
      return $this;
    }
    
    public function insert($string, $offset, $overwrite = self::DO_NOT_OVERWRITE_FOLLING_CHARACTERS) {
      $this->checkOffset($offset, self::OFFSET_EXTENDED);
      if($string instanceof String) {
        $string = $string->getValue();
      }
      if($overwrite) {
        $prefix = mb_substr($this->getValue(), 0, $offset, $this->getEncoding());
        $postfix = mb_substr(
          $this->getValue(), 
          $offset + mb_strlen($string, $this->getEncoding()), 
          $this->getLength(), //rest of the string can never be longer than this
          $this->getEncoding()
        );
      } else {
        $value = $this->getValue();
        $prefix = mb_substr($value, 0, $offset, $this->getEncoding());
        $postfix = mb_substr($value, $offset, $this->getLength(), $this->getEncoding());
      }
      $this->setValue($prefix . $string . $postfix);
      
      return $this;
    }
    
    public function append($string) {
      $this->insert($string, $this->getLength());
      return $this;
    }
    
    public function countBytes() {
      return strlen($this->getValue());
    }
    
    public function getBytes() {
      $ret = array();
      $value = $this->getValue();
      for($i = 0; $i < strlen($value); $i++) {
        $ret[] = $value[$i];
      }
      return $ret;
    }
    
    public function __toString() {
      return $this->getValue();
    }
  
    private function pad($padLength, $padString =' ', $padType = STR_PAD_RIGHT) {
      $padCount = max(0, $padLength - $this->getLength());
      if($padCount > 0) {
        $value = $this->getValue();
        $padding = '';
        for($i = 0; $i < $padCount; $i++) {
          $padding .= $padString;
        }
        
        switch($padType) {
          case STR_PAD_LEFT:
            $value = $padding . $value;
            break;
          case STR_PAD_RIGHT:
            $value = $value . $padding;
            break;
          case STR_PAD_BOTH:
            $left = $right = '';
            if($padCount % 2 == 0) {
              $left = $right = mb_substr($padding, 0, $padCount / 2, $this->getEncoding());
            } else {
              $leftCount = floor($padCount / 2);
              $left = mb_substr($padding, 0, $leftCount, $this->getEncoding());
              $right = $left . $padString;
            }
            $value = $left . $value . $right;
            break;
          default:
            throw new StringException('Invalid padType given: ' . $padType);
            break;
        }
        $this->setValue($value);
      }
      
      return $this;
    }
    
    public function lpad($padLength, $padString = ' ') {
      return $this->pad($padLength, $padString, STR_PAD_LEFT);
    }
    
    public function mpad($padLength, $padString = ' ') {
      return $this->pad($padLength, $padString, STR_PAD_BOTH);
    }
    
    public function rpad($padLength, $padString = ' ') {
      return $this->pad($padLength, $padString, STR_PAD_RIGHT);
    }
    
    public function startsWith($string, $offset = 0) {
      $this->checkOffset($offset);
      return mb_substr(
        $this->getValue(), 
        $offset, 
        mb_strlen($string, $this->getEncoding()), 
        $this->getEncoding()
      ) == $string;
    }
    
    public function endsWith($string) {
      return mb_substr(
        $this->getValue(), 
        $this->getLength() - mb_strlen($string, $this->getEncoding()), 
        $this->getLength(), //String cannot be longer than this
        $this->getEncoding()
      ) == $string;
    }
    
    public function detectLineBreaks() {
      $ret = "\r\n";
      $encoding = $this->getEncoding();
  
      $backslashRNCount = mb_substr_count($this->__toString(), "\r\n", $encoding);
      $backslashRCount = mb_substr_count($this->__toString(), "\r", $encoding) - $backslashRNCount;
      $backslashNCount = mb_substr_count($this->__toString(), "\n", $encoding) - $backslashRNCount;
      
      if($backslashRCount > $backslashNCount && $backslashRCount > $backslashRNCount) {
        $ret = "\r";
      } elseif($backslashNCount > $backslashRCount && $backslashNCount > $backslashRNCount) {
        $ret = "\n";
      }
  
      return $ret;
    }
    
    public function normalizeLineBreaks($lineEnding = self::LINE_ENDING_N) {
      switch($lineEnding) {
        case self::LINE_ENDING_N:
          $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_N, $this->getValue()));
          $this->setValue(str_replace(self::LINE_ENDING_R, self::LINE_ENDING_N, $this->getValue()));
          break;
        case self::LINE_ENDING_R:
          $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_R, $this->getValue()));
          $this->setValue(str_replace(self::LINE_ENDING_N, self::LINE_ENDING_R, $this->getValue()));
          break;
        case self::LINE_ENDING_RN:
          $this->setValue(str_replace(self::LINE_ENDING_RN, self::LINE_ENDING_N, $this->getValue()));
          $this->setValue(str_replace(self::LINE_ENDING_R, self::LINE_ENDING_N, $this->getValue()));
          $this->setValue(str_replace(self::LINE_ENDING_N, self::LINE_ENDING_RN, $this->getValue()));
          break;
      }
      return $this;
    }
    
    public function wordwrap($lineEnding = self::LINE_ENDING_N, $width=self::TXT_WIDTH) {
      throw new StringException('Not implemented yet!');
    }
      
    public static function setDefaultEncoding($encoding) {
      self::$defaultEncoding = $encoding;
    }
    
    public static function getDefaultEncoding() {
      return self::$defaultEncoding;
    }
  
    public static function factory($value, $encoding=null) {
      return new String($value, $encoding);
    }
    
  }
  
  class Text
  {
      private $text;
  
      public function __construct($val = null)
      {
          if($val != "" || $val != null)
          {
              $this->text = $val;
          }
          else
          {
              $this->text = "";
          }
      }
  
      public function equals($val)
      {
         return (($this->text == $val) ? true : false);
      }
  
      public function value()
      {
          return $this->text;
      }
  
      public function clear()
      {
          $this->text = "";
      }
  
      public function assign($val)
      {
          $this->text = $val;
      }
  
      public function length()
      {
          $rtn = strlen($this->text);
          return $rtn;
      }
  
      public function upperCase($type = "all")
      {
          $type = strtolower($type);
  
          switch($type)
          {
              case "first":
                  $this->text = ucfirst($this->text);
              break;
  
              case "words":
                  $this->text = ucwords($this->text);
              break;
  
              case "all":
                  $this->text = strtoupper($this->text);
              break;
  
              default:
                  $this->text = strtoupper($this->text);
              break;
          } 
      }
  
      public function lowerCase($type = "all")
      {
          $type = strtolower($type);
  
          switch($type)
          {
              case "first":
                  $this->text = lcfirst($this->text);
              break;
  
              case "all":
                  $this->text = strtolower($this->text);
              break;
  
              default:
                  $this->text = strtolower($this->text);
              break;
          }
      }
  
      public function reverse($words = TRUE, $sep = ' ')
      {
          $tmp;
  
          if($words == TRUE)
          {
              $tmp = explode($sep, $this->text);
              $tmp = array_reverse($tmp);
              $this->text = implode($sep, $tmp);
          }
          else if($words == FALSE)
          {
              $this->text = strrev($this->text);
          }
          else
          {
              $this->text = strrev($this->text);
          }
      }
  
      public function combine($txt)
      {
          $this->text = $this->text . $txt;
      }
  
      public function contains($txt)
      {
          $p = strpos($this->text,$txt);
          $rtn = FALSE;
  
          if($p === FALSE)
          {
              $rtn = FALSE;
          }
          else
          {
              $rtn = TRUE;
          }
  
          return $rtn;
      }
  
      public function chunk($start,$end)
      {
          $rtn = "";
  
          if(!is_numeric($start) && !is_numeric($end))
          {
              $rtn = "";
          }
          else
          {
              $rtn = substr($this->text,$start,$end);
          }
  
          return $rtn;
      }
  
      public function replace($searchTxt, $replacement)
      {
          $this->text = str_replace($searchTxt, $replacement, $this->text);
      }
  
      public function characters($len = 0)
      {
          $rtn;
  
          if($len > 0)
          {
              $rtn = str_split($this->text,$len);
          }
          else
          {
              $rtn = str_split($this->text);
          }
  
          return $rtn;
      }
      public function slim($chars = null)
      {
          if(is_null($chars) || $chars != "")
          {
             $this->text = trim($this->text);
          }
          else
          {
              $this->text = trim($this->text,$chars);
          }
      }
  }
  
  /*
  mb_check_encoding — Check if strings are valid for the specified encoding
  mb_chr — Return character by Unicode code point value
  mb_convert_case — Perform case folding on a string
  mb_convert_encoding — Convert a string from one character encoding to another
  mb_convert_kana — Convert "kana" one from another ("zen-kaku", "han-kaku" and more)
  mb_convert_variables — Convert character code in variable(s)
  mb_decode_mimeheader — Decode string in MIME header field
  mb_decode_numericentity — Decode HTML numeric string reference to character
  mb_detect_encoding — Detect character encoding
  mb_detect_order — Set/Get character encoding detection order
  mb_encode_mimeheader — Encode string for MIME header
  mb_encode_numericentity — Encode character to HTML numeric string reference
  mb_encoding_aliases — Get aliases of a known encoding type
  mb_ereg — Regular expression match with multibyte support
  mb_ereg_match — Regular expression match for multibyte string
  mb_ereg_replace — Replace regular expression with multibyte support
  mb_ereg_replace_callback — Perform a regular expression search and replace with multibyte support using a callback
  mb_ereg_search — Multibyte regular expression match for predefined multibyte string
  mb_ereg_search_getpos — Returns start point for next regular expression match
  mb_ereg_search_getregs — Retrieve the result from the last multibyte regular expression match
  mb_ereg_search_init — Setup string and regular expression for a multibyte regular expression match
  mb_ereg_search_pos — Returns position and length of a matched part of the multibyte regular expression for a predefined multibyte string
  mb_ereg_search_regs — Returns the matched part of a multibyte regular expression
  mb_ereg_search_setpos — Set start point of next regular expression match
  mb_eregi — Regular expression match ignoring case with multibyte support
  mb_eregi_replace — Replace regular expression with multibyte support ignoring case
  mb_get_info — Get internal settings of mbstring
  mb_language — Set/Get current language
  mb_list_encodings — Returns an array of all supported encodings
  mb_ord — Get Unicode code point of character
  mb_output_handler — Callback function converts character encoding in output buffer
  mb_parse_str — Parse GET/POST/COOKIE data and set global variable
  mb_preferred_mime_name — Get MIME charset string
  mb_regex_encoding — Set/Get character encoding for multibyte regex
  mb_regex_set_options — Set/Get the default options for mbregex functions
  mb_scrub — Replace ill-formed byte sequences with the substitute character
  mb_send_mail — Send encoded mail
  mb_split — Split multibyte string using regular expression
*/