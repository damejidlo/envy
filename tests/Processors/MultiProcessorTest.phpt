<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Processors;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\Processors\MultiProcessor;
use Damejidlo\Envy\Processors\ValidatorProcessor;
use Damejidlo\Envy\ValueProviderInterface;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\Fixtures\FailingValueProvider;
use DamejidloTests\Envy\Fixtures\IncrementProcessor;
use DamejidloTests\Envy\TestCase;
use Nette\Utils\AssertionException;
use Tester\Assert;



/**
 * @testCase
 */
class MultiProcessorTest extends TestCase
{

	/**
	 * @dataProvider getDataForProcessorsOrder
	 * @param ValueProviderInterface $valueProvider
	 * @param mixed $expectedValue
	 */
	public function testProcessorsOrder(ValueProviderInterface $valueProvider, $expectedValue) : void
	{
		$processor = new MultiProcessor(new ValidatorProcessor('int:0..0'), new IncrementProcessor());
		$processor = $processor->withValidator('int:1..1');
		$processor = $processor->withFallback(41);
		$processor = $processor->withAddedProcessor(new IncrementProcessor());

		Assert::same($expectedValue, $processor->process('foo', $valueProvider));
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
		$processor = new MultiProcessor();
		$processorWithIncrement = $processor->withAddedProcessor(new IncrementProcessor());

		Assert::same(1, $processorWithIncrement->process('foo', $value));

		// immutability test
		Assert::same(0, $processor->process('foo', $value));
	}



	public function testWithFallback() : void
	{
		$value = new FailingValueProvider();
		$processor = new MultiProcessor();
		$processorWithFallback = $processor->withFallback('default');

		Assert::same('default', $processorWithFallback->process('foo', $value));

		// immutability test
		Assert::exception(
			function () use ($processor, $value) : void {
				$processor->process('foo', $value);
			},
			EnvironmentVariableNotFoundException::class,
			"Environment variable 'foo' not found.",
		);
	}



	public function testWithValidator() : void
	{
		$value = new SingleValueProvider('foo');
		$processor = new MultiProcessor();
		$processorWithValidation = $processor->withValidator('url');

		Assert::exception(
			function () use ($processorWithValidation, $value) : void {
				$processorWithValidation->process('foo', $value);
			},
			AssertionException::class,
			"The foo expects to be url, string 'foo' given.",
		);

		// immutability test
		Assert::same('foo', $processor->process('foo', $value));
	}

}



(new MultiProcessorTest())->run();
