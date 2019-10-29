<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\ValueProviders;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\Processors\ValidatorProcessor;
use Damejidlo\Envy\ValueProviderInterface;
use Damejidlo\Envy\ValueProviders\Loader;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\Fixtures\FailingValueProvider;
use DamejidloTests\Envy\Fixtures\IncrementProcessor;
use DamejidloTests\Envy\TestCase;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class LoaderTest extends TestCase
{

	/**
	 * @dataProvider getDataForProcessorsOrder
	 * @param ValueProviderInterface $valueProvider
	 * @param mixed $expectedValue
	 */
	public function testProcessorsOrder(ValueProviderInterface $valueProvider, $expectedValue) : void
	{
		$loader = new Loader($valueProvider, new ValidatorProcessor('int:0..0'), new IncrementProcessor());
		$loader = $loader->withValidator('int:1..1');
		$loader = $loader->withFallback(41);
		$loader = $loader->withAddedProcessor(new IncrementProcessor());

		Assert::same($expectedValue, $loader->get('foo'));
	}



	/**
	 * @return mixed[]
	 */
	protected function getDataForProcessorsOrder() : array
	{
		return [
			[
				'valueProvider' => new SingleValueProvider(0),
				'expectedValue' => 2,
			],
			[
				'valueProvider' => new FailingValueProvider(),
				'expectedValue' => 42,
			],
		];
	}



	public function testWithAddedProcessor() : void
	{
		$value = new SingleValueProvider(0);
		$loader = new Loader($value);
		$loaderWithIncrement = $loader->withAddedProcessor(new IncrementProcessor());

		Assert::same(1, $loaderWithIncrement->get('foo'));

		// immutability test
		Assert::same(0, $loader->get('foo'));
	}



	public function testWithFallback() : void
	{
		$value = new FailingValueProvider();
		$loader = new Loader($value);
		$loaderWithFallback = $loader->withFallback('default');

		Assert::same('default', $loaderWithFallback->get('foo'));

		// immutability test
		Assert::exception(
			function () use ($loader) : void {
				$loader->get('foo');
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'foo' not found.",
		);
	}



	public function testWithValidator() : void
	{
		$value = new SingleValueProvider('foo');
		$loader = new Loader($value);
		$loaderWithValidation = $loader->withValidator('url');

		Assert::exception(
			function () use ($loaderWithValidation) : void {
				$loaderWithValidation->get('foo');
			},
			AssertionException::class,
			"The foo expects to be url, string 'foo' given.",
		);

		// immutability test
		Assert::same('foo', $loader->get('foo'));
	}

}



(new LoaderTest())->run();
