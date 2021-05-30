# Minix
Code minify

### Install
`composer require qexyorg/minix`

### Example
```php
<?php

use qexyorg\Minix\Minix;

require_once('vendor/autoload.php');

$filename = 'myfile.php';

$code = Minix::Php()->file($filename);
// or $code->code(/* String format */);

$min = $code->minify();
```

### Current supported languages
- PHP

### TODO
- CSS
- JavaScript