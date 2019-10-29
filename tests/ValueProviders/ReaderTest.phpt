<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\ValueProviders;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\ValueProviders\Reader;
use DamejidloTests\Envy\TestCase;
use Tester\Assert;



/**
 * @testCase
 */
class ReaderTest extends TestCase
{

	private const KNOWN = 'KNOWN';
	private const UNKNOWN = 'UNKNOWN';



	protected function setUp() : void
	{
		parent::setUp();
		putenv('KNOWN=');
		putenv('UNKNOWN');
	}



	public function testKnownEmptyValue() : void
	{
		$reader = new Reader();

		Assert::true($reader->exists(self::KNOWN));
		Assert::same('', $reader->get(self::KNOWN));
	}



	public function testNotSetVariable() : void
	{
		$reader = new Reader();

		Assert::false($reader->exists(self::UNKNOWN));

		Assert::exception(
			function () use ($reader) : void {
				$reader->get(self::UNKNOWN);
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'UNKNOWN' not found.",
		);
	}

}



(new ReaderTest())->run();
