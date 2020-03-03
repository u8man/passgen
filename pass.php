#!/usr/bin/env php

<?php

/**
 *  Генератор паролей.
 *
 * @version 0.1.5
 *
 *  Примеры:
 *      php pass.php 16 4   // 16 - количество символов, 4 - тип пароля
 *      php pass.php -r     // генерирует пароль длинной от 10 до 16 символов с 4 типом
 */

/* ---- */

// Набор символов
define('PG_LOW_CHAR', 'qwertyuiopasdfghjklzxcvbnm');
define('PG_UP_CHAR', 'QWERTYUIOPASDFGHJKLZXCVBNM');
define('PG_NUM_CHAR', '0123456789');
define('PG_SYM_CHAR', '~!@#$%^&*()-+=_|/[]{}?:><.,\\');

/* ---- */

/**
 * @return false|string
 */
function in()
{
    if (extension_loaded('readline')) {
        return readline();
    }

    return fgets(STDIN);
}

/**
 * @param string $text
 * @param string|null $foreground
 * @return void
 */
function out($text, $foreground = null)
{
    if ($foreground) {
        $text = "\033[". $foreground."m".$text."\033[0m";
    }

    fwrite(STDOUT, $text);
}

/**
 * @param string $text
 * @param string|null $foreground
 * @return void
 */
function out_ln($text = '', $foreground = null)
{
    out($text. PHP_EOL, $foreground);
}

/**
 * @param int $lenght
 * @param int $type
 * @return string
 */
function generate($lenght, $type)
{
	switch ($type) {
        case 1: // [a-z]
            $chars = PG_LOW_CHAR;
            break;
        case 2: // [a-z A-Z]
            $chars = PG_LOW_CHAR . PG_UP_CHAR;
            break;
        case 3: // [a-z 0-9]
            $chars = PG_LOW_CHAR . PG_NUM_CHAR;
            break;
        case 4: // [a-z A-Z 0-9]
            $chars = PG_LOW_CHAR . PG_UP_CHAR . PG_NUM_CHAR;
            break;
        case 5: // [A-Z 0-9]
            $chars = PG_UP_CHAR . PG_NUM_CHAR;
            break;
        case 6: // [a-z A-Z 0-9 @#$?&]
            $chars = PG_LOW_CHAR . PG_UP_CHAR . PG_NUM_CHAR . PG_SYM_CHAR;
            break;
		default:
			$chars = '';
    }

    $chars_len = strlen($chars);
	$str = '';

    while (true) {
        $str .= $chars[mt_rand(0, $chars_len - 1)];
        if (strlen($str) >= $lenght) {
            break;
        }
    }

    return $str;
}

/* ---- */

$args = $argv ?: [];

$type = 0;
$lenght = 0;

if (isset($args[2]) && is_numeric($args[2])) {
    $type = $args[2];
}

if(isset($args[1]) && $args[1] == '-r') {
    $lenght = mt_rand(10, 16);
    $type = 4;
} elseif (isset($args[1]) && is_numeric($args[1])) {
    $lenght = $args[1];
}

while (true) {
    if ($lenght < 3 || $lenght > 32) {
        if ($lenght == 0) {
            out_ln('Введите желаемое количество символов (3-32):');
        } else {
            out_ln('Введите корректную длинну пароля(3-32):');
        }
        out('> ');
        $lenght = in();
    } elseif ($type < 1 || $type > 6 ) {
        if ($type == 0) {
            out_ln('Выберите тип пароля (1-6):');
        } else {
            out_ln('Выберите корректный тип пароля (1-6):');
        }
        out_ln('1 - [a-z]');
        out_ln('2 - [a-z A-Z]');
        out_ln('3 - [a-z 0-9]');
        out_ln('4 - [a-z A-Z 0-9]');
        out_ln('5 - [A-Z 0-9]');
        out_ln('6 - [a-z A-Z 0-9 @#$?&]');
        out('> ');
        $type = in();
    } else {
        break;
    }
}

$password = generate($lenght, $type);

out('Ваш пароль ');
out($password, '0;35'); // фиолетовый

$copy = false;

switch (php_uname('s') ) {
    case 'Darwin':
        exec('echo "'.$password.'" | pbcopy');
        $copy = true;
        break;
    case 'Linux':
        exec('echo "'.$password.'" | xclip');
        $copy = true;
        break;
}

if ($copy) {
    out(' скопирован в буфер обмена.');
}

out_ln();

