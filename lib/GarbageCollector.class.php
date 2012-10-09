<?php

class WPLessGarbagecollector
{
	protected $configuration;

	public function __construct(WPLessConfiguration $configuration)
	{
		$this->configuration = $configuration;
	}

	public function clean()
	{
		update_option('wp_less_gc', date('U'));
	}

	protected function getOutdatedFiles()
	{

	}

	protected function deleteFiles(array $files)
	{

	}
}
