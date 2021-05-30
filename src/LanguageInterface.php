<?php

/**
 *
 * Language interface
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

namespace qexyorg\Minix;

interface LanguageInterface {


    public function __construct(?string $filename);


    public function minify() : string;


    public function code(string $code) : self;


    public function file(string $filename) : self;


    public function removeComments(bool $value) : self;


}

?>