<?php

/**
 *
 * Php language class of Minix
 *
 *
 * @author Qexy admin@qexy.org
 *
 *
 * @package qexyorg\Minix
 *
 *
 * @license MIT
 *
 *
 * @version 1.1.0
 *
 */

namespace qexyorg\Minix\Types;

use qexyorg\Minix\LanguageInterface;

class Php implements LanguageInterface {


    private $code = '';


    private $comments = true;


    private $storage = [];


    public function __construct(?string $filename = null){
        if(!is_null($filename)){
            $this->file($filename);
        }
    }


    public function code(string $code) : LanguageInterface {

        $this->code = $code;

        return $this;
    }


    public function file(string $filename) : LanguageInterface {

        $this->code = @file_get_contents($filename);

        return $this;
    }


    private function removeSpaces(string $str) : string {
        $removeSpaces = ['=', '.', ',', ':', '{', '}', '[', ']', '+', '-', '!', '|', '>', '<', '(', ')', '?', ';', '$', '%', '^', '&', '*'];

        foreach($removeSpaces as $symbols){
            $quoted = preg_quote($symbols);
            $str = preg_replace("/(\s+)?{$quoted}(\s+)?/i", $symbols, $str);
        }

        return preg_replace('/\s{2,}/i', ' ', $str);
    }


    private function str_replace_first(string $from, string $to, string $content) : string {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }


    private function removeCommentary(string $str) : string {
        $newStr  = '';

        $commentTokens = array(T_COMMENT);

        if (defined('T_DOC_COMMENT')) {
            $commentTokens[] = T_DOC_COMMENT; // PHP 5
        }

        if (defined('T_ML_COMMENT')) {
            $commentTokens[] = T_ML_COMMENT;  // PHP 4
        }

        $tokens = token_get_all($str);

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    continue;
                }

                $token = $token[1];
            }

            $newStr .= $token;
        }

        return $newStr;
    }

    private function removeBorders(string $str) : string {
        $str = preg_replace('/^\<\?php/isu', '', $str);

        return preg_replace('/\?\>$/isu', '', $str);
    }


    public function minify() : string {
        $this->code = $this->removeCommentary($this->code);

        $this->code = $this->removeBorders($this->code);

        $split = explode(PHP_EOL, $this->code);

        $keys = [];

        $data = [];

        foreach($split as $str){

            $str = trim($str);

            if($str == ''){ continue; }

            $searchQuote = strripos($str, '\'');
            $searchDoubleQuote = strripos($str, '"');
            $searchSleshedQuote = strripos($str, '\\\'');
            $searchSleshedDoubleQuote = strripos($str, '\\"');

            if($searchQuote !== false && ($searchDoubleQuote === false && $searchSleshedQuote === false && $searchSleshedDoubleQuote === false)){

                preg_match_all('/\'([^\']+)?\'/isu', $str, $matches);

                foreach($matches[0] as $string){
                    $key = '~'.md5(mt_rand(0, 99999999)).'~';

                    $keys[$key] = $string;

                    $str = $this->str_replace_first($string, $key, $str);
                }

                $str = $this->removeSpaces($str);
            }elseif($searchDoubleQuote !== false && ($searchQuote === false && $searchSleshedQuote === false && $searchSleshedDoubleQuote === false)){

                preg_match_all('/\"([^\"]+)?\"/isu', $str, $matches);

                foreach($matches[0] as $string){
                    $key = '~'.md5(mt_rand(0, 99999999)).'~';

                    $keys[$key] = $string;

                    $str = $this->str_replace_first($string, $key, $str);
                }

                $str = $this->removeSpaces($str);
            }elseif($searchQuote === false && $searchDoubleQuote === false){
                $str = $this->removeSpaces($str);
            }

            $str = trim($str);

            if($str == ''){ continue; }


            $data[] = $str;
        }

        $this->code = implode(PHP_EOL, $data);

        foreach($keys as $k => $v){
            $this->code = $this->str_replace_first($k, $v, $this->code);
        }

        return "<?php {$this->code}";
    }
}

?>
