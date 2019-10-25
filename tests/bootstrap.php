<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

// detect PHPStan
if (getenv('IS_PHPSTAN') !== FALSE) {
	$_ENV['IS_PHPSTAN'] = in_array(strtolower(getenv('IS_PHPSTAN')), ['1', 'true', 'yes', 'on'], TRUE);
} else {
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$_ENV['IS_PHPSTAN'] = (bool) preg_match('~[/\\\\]phpstan(?:\.phar)?$~', end($trace)['file'] ?? '');
}


// configure environment
if (!$_ENV['IS_PHPSTAN']) {
	Tester\Environment::setup();
}


// create temporary directory
define('TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
if (!$_ENV['IS_PHPSTAN']) {
	@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
	Tester\Helpers::purge(TEMP_DIR);
	ini_set('session.save_path', TEMP_DIR);
}
