<?php

/**
 *
 * Main class of Minix
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

use qexyorg\Minix\Types\Php;

class Minix {

    public static function php() : LanguageInterface {
        return new Php();
    }

}

?>