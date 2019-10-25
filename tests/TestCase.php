<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy;

use Tester;



abstract class TestCase extends Tester\TestCase
{

	public function run() : void
	{
		if ($_ENV['IS_PHPSTAN'] ?? FALSE) {
			return;
		}
		parent::run();
	}

}
