<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class ArrayPartition extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'nova:array-partition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new metric (single array partition) class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Metric';

	/**
	 * @param string $name
	 *
	 * @return mixed|string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function buildClass($name)
	{
		$stub = parent::buildClass($name);

		$key = preg_replace('/[^a-zA-Z0-9]+/', '', $this->argument('name'));

		return str_replace('uri-key', Str::kebab($key), $stub);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__.'/stubs/array-partition.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Nova\Metrics';
	}
}
