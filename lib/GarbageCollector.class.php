<?php

class WPLessGarbagecollector
{
	protected $configuration;

	public function __construct(WPLessConfiguration $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * Performs the cleanup of outdated CSS files
	 *
	 */
	public function clean()
	{
		$outdated_files = $this->getOutdatedFiles($this->configuration->getTtl());

		if (!empty($outdated_files))
		{
			$this->deleteFiles($outdated_files);
		}
	}

	/**
	 * Retrieves old CSS files and list them
	 *
	 * @param $ttl int
	 * @return array
	 */
	protected function getOutdatedFiles($ttl)
	{
		$outdated = array();
		$time = time();
		$dir = new RecursiveDirectoryIterator($this->configuration->getUploadDir());

		/*
		 * Collecting CSS files
		 */
		$files = new RegexIterator(
			new RecursiveIteratorIterator($dir),
			'#.css#U',
			RecursiveRegexIterator::ALL_MATCHES
		);

		/*
		 * Checking expiry
		 */
		foreach ($files as $filepath => $match)
		{
			(filemtime($filepath) + $ttl < $time) ? array_push($outdated, $filepath) : null;
		}

		return $outdated;
	}

	/**
	 * Remove a bunch of files
	 *
	 * @protected
	 * @param array $files
	 * @return array
	 */
	protected function deleteFiles(array $files)
	{
		return array_map('unlink', $files);
	}
}
