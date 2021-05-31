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
 * @version 1.0.0
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


    public function removeComments(bool $value) : LanguageInterface {

        $this->comments = $value;

        return $this;
    }


    public function minify() : string {

        $this->code = preg_replace('/^([\s\n]+)?\<\?(php)?(.*)\?\>([\s\n]+)?$/isu', '$3', $this->code);

        $this->code = trim($this->code);

        $this->storage['strings'] = [];

        $this->code = preg_replace_callback('/\"(.*)[^\\\]\"/U', function($matches){
            $md5 = md5($matches[0].hrtime(true));

            $this->storage['strings'][$md5] = $matches[0];

            return $md5;
        }, $this->code);


        $this->code = preg_replace_callback("/'(.*)[^\\\]'/U", function($matches){
            $md5 = md5($matches[0].hrtime(true));

            $this->storage['strings'][$md5] = $matches[0];

            return $md5;
        }, $this->code);

        if($this->comments){
            $this->code = $this->replaceComments($this->code);
        }

        $this->code = $this->replaceSpaces($this->code);

        $this->code = "<?php {$this->code} ?>";
        
        return $this->code;
    }


    private function replaceComments(string  $code) : string {
        $code = preg_replace('/\/\*.*\*\//isu', '', $code);

        return preg_replace('/(\/\/[^\n]+)|(\#[^\n]+)/', '', $code);
    }


    private function replaceSpaces(string $code) : string {

        $code = preg_replace('/[\s\n]+/', ' ', $code);

        $symbols = '{}|,.;/\:=()><[]+-*?&%@!';

        $symbols = str_split(addcslashes($symbols, $symbols), 2);

        $code = preg_replace('/([\s\n]+)?('.implode('|', $symbols).')([\s\n]+)?/isu', '$2', $code);

        return strtr($code, $this->storage['strings']);
    }

}

?>
