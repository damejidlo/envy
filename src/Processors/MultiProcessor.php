<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\Processors;

use Damejidlo\Envy\ProcessorInterface;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;



final class MultiProcessor implements ProcessorInterface
{

	use SmartObject;

	/**
	 * @var ProcessorInterface[]
	 */
	private $processors;



	public function __construct(ProcessorInterface ...$processors)
	{
		$this->processors = $processors;
	}



	public function withAddedProcessor(ProcessorInterface $processor) : MultiProcessor
	{
		$clone = clone $this;
		$clone->processors[] = $processor;

		return $clone;
	}



	/**
	 * @param mixed $fallback
	 * @return MultiProcessor
	 */
	public function withFallback($fallback) : MultiProcessor
	{
		return $this->withAddedProcessor(new FallbackProcessor($fallback));
	}



	public function withValidator(string $type) : MultiProcessor
	{
		return $this->withAddedProcessor(new ValidatorProcessor($type));
	}



	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed
	 */
	public function process(string $name, ValueProviderInterface $valueProvider)
	{
		if (count($this->processors) === 0) {
			return $valueProvider->get($name);
		}

		return $this->createRunner($valueProvider)->get($name);
	}



	private function createRunner(ValueProviderInterface $valueProvider) : ValueProviderInterface
	{
		$processors = array_reverse($this->processors);
		return new class ($valueProvider, ...$processors) implements ValueProviderInterface
		{

			/**
			 * @var ValueProviderInterface
			 */
			private $valueProvider;

			/**
			 * @var ProcessorInterface[]
			 */
			private $processors;



			public function __construct(ValueProviderInterface $valueProvider, ProcessorInterface ...$processors)
			{
				$this->valueProvider = $valueProvider;
				$this->processors = $processors;
			}



			/**
			 * @param string $name
			 * @return mixed
			 */
			public function get(string $name)
			{
				$processor = current($this->processors);

				if ($processor !== FALSE) {
					next($this->processors);
					return $processor->process($name, $this);
				}

				return $this->valueProvider->get($name);
			}

		};
	}

}
