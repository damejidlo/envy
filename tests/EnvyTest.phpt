<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy;

require_once __DIR__ . '/bootstrap.php';

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\Envy;
use Damejidlo\Envy\LoaderFactory;
use Damejidlo\Envy\ValueProviders\Reader;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class EnvyTest extends TestCase
{

	protected function setUp() : void
	{
		parent::setUp();
		putenv('VAR_UNKNOWN');
		putenv('VAR_EMPTY=');
		putenv('VAR_STRING=foo');
		putenv('VAR_INT=42');
		putenv('VAR_FLOAT=3.1415');
		putenv('VAR_BOOL=true');
		putenv('VAR_STRING_ARRAY=foo, bar');
		putenv('VAR_INT_ARRAY=81,169,361');
		putenv('VAR_FLOAT_ARRAY=-1,   2.71828,    3.1415');
	}



	public function testExists() : void
	{
		$envy = $this->createEnvy();
		Assert::false($envy->exists('VAR_UNKNOWN'));
		Assert::true($envy->exists('VAR_STRING'));
	}



	public function testString() : void
	{
		$envy = $this->createEnvy();

		Assert::same('foo', $envy->getString('VAR_STRING'));
		Assert::same('foo', $envy->getStringOrNull('VAR_STRING'));

		Assert::same('default', $envy->getString('VAR_UNKNOWN', 'default'));
		Assert::same(NULL, $envy->getStringOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getString('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);
	}



	public function testBool() : void
	{
		$envy = $this->createEnvy();

		Assert::same(TRUE, $envy->getBool('VAR_BOOL'));
		Assert::same(TRUE, $envy->getBoolOrNull('VAR_BOOL'));

		Assert::same(FALSE, $envy->getBool('VAR_UNKNOWN', FALSE));
		Assert::same(NULL, $envy->getBoolOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getBool('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);

		Assert::exception(
			function () use ($envy) : void {
				$envy->getBool('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be one of 'true', 'false', '1', '0', 'yes', 'no', but got ''.",
		);
		Assert::exception(
			function () use ($envy) : void {
				$envy->getBoolOrNull('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be one of 'true', 'false', '1', '0', 'yes', 'no', but got ''.",
		);
	}



	public function testInt() : void
	{
		$envy = $this->createEnvy();

		Assert::same(42, $envy->getInt('VAR_INT'));
		Assert::same(42, $envy->getIntOrNull('VAR_INT'));

		Assert::same(1, $envy->getInt('VAR_UNKNOWN', 1));
		Assert::same(NULL, $envy->getIntOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getInt('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);

		Assert::exception(
			function () use ($envy) : void {
				$envy->getInt('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be numericint, string '' given.",
		);
		Assert::exception(
			function () use ($envy) : void {
				$envy->getIntOrNull('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be numericint, string '' given.",
		);
	}



	public function testFloat() : void
	{
		$envy = $this->createEnvy();

		Assert::equal(3.1415, $envy->getFloat('VAR_FLOAT'));
		Assert::equal(3.1415, $envy->getFloatOrNull('VAR_FLOAT'));

		Assert::equal(8.314472, $envy->getFloat('VAR_UNKNOWN', 8.314472));
		Assert::same(NULL, $envy->getFloatOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloat('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);

		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloat('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be numeric, string '' given.",
		);
		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloatOrNull('VAR_EMPTY');
			},
			AssertionException::class,
			"The VAR_EMPTY expects to be numeric, string '' given.",
		);
	}



	public function testStringArray() : void
	{
		$envy = $this->createEnvy();

		Assert::same([], $envy->getStringArray('VAR_EMPTY'));
		Assert::same(['foo', 'bar'], $envy->getStringArray('VAR_STRING_ARRAY'));
		Assert::same(['foo', 'bar'], $envy->getStringArrayOrNull('VAR_STRING_ARRAY'));

		Assert::same(['lorem', 'ipsum'], $envy->getStringArray('VAR_UNKNOWN', ['lorem', 'ipsum']));
		Assert::same(NULL, $envy->getStringArrayOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getStringArray('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);
	}



	public function testIntArray() : void
	{
		$envy = $this->createEnvy();

		Assert::same([], $envy->getIntArray('VAR_EMPTY'));
		Assert::same([81, 169, 361], $envy->getIntArray('VAR_INT_ARRAY'));
		Assert::same([81, 169, 361], $envy->getIntArrayOrNull('VAR_INT_ARRAY'));

		Assert::same([42, 24], $envy->getIntArray('VAR_UNKNOWN', [42, 24]));
		Assert::same(NULL, $envy->getIntArrayOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getIntArray('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);

		Assert::exception(
			function () use ($envy) : void {
				$envy->getIntArray('VAR_STRING');
			},
			AssertionException::class,
			"The item of VAR_STRING expects to be numericint, string 'foo' given.",
		);
		Assert::exception(
			function () use ($envy) : void {
				$envy->getIntArrayOrNull('VAR_STRING');
			},
			AssertionException::class,
			"The item of VAR_STRING expects to be numericint, string 'foo' given.",
		);
	}



	public function testFloatArray() : void
	{
		$envy = $this->createEnvy();

		Assert::same([], $envy->getFloatArray('VAR_EMPTY'));
		Assert::equal([-1.0, 2.71828, 3.1415], $envy->getFloatArray('VAR_FLOAT_ARRAY'));
		Assert::equal([-1.0, 2.71828, 3.1415], $envy->getFloatArrayOrNull('VAR_FLOAT_ARRAY'));

		Assert::equal([4.2, 2.4], $envy->getFloatArray('VAR_UNKNOWN', [4.2, 2.4]));
		Assert::same(NULL, $envy->getFloatArrayOrNull('VAR_UNKNOWN'));

		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloatArray('VAR_UNKNOWN');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'VAR_UNKNOWN' not found.",
		);

		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloatArray('VAR_STRING');
			},
			AssertionException::class,
			"The item of VAR_STRING expects to be numeric, string 'foo' given.",
		);
		Assert::exception(
			function () use ($envy) : void {
				$envy->getFloatArrayOrNull('VAR_STRING');
			},
			AssertionException::class,
			"The item of VAR_STRING expects to be numeric, string 'foo' given.",
		);
	}



	private function createEnvy() : Envy
	{
		$reader = new Reader();
		$loaderFactory = new LoaderFactory($reader);
		return new Envy($reader, $loaderFactory);
	}

}



(new EnvyTest())->run();
