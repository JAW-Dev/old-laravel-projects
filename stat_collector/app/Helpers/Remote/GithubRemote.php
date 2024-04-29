<?php

namespace App\Helpers\Remote;

use App\Helpers\Filters\VersionFilter;

/**
 * Class used to connect to the github api and perform various retrieval and parsing actions
 *
 * @package App\Helpers
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class GithubRemote extends Remote {
	/**
	 * The oauth2 app token used for querying the github api
	 *
	 * @var string
	 */
	private $oauth_token = '';

	/**
	 * The path relative to the github base url for the api
	 *
	 * @var string
	 */
	private $path = '';

	/**
	 * The github api base url
	 *
	 * @var string
	 */
	private $base_url = 'https://api.github.com/';

	/**
	 * GithubRemote constructor.
	 *
	 * @param string $oauth_token
	 * @param string $path
	 */
	public function __construct(string $oauth_token, string $path) {
		$this->oauth_token = $oauth_token;
		$this->path = $path;
	}

	/**
	 * Fetch the unfiltered api data
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function fetch_api_data(array $options): array {
		$url = $this->construct_github_url($options);

		return static::fetch_remote_json($url);
	}

	/**
	 * Construct the github url with the base url and the api path
	 *
	 * @param array $options
	 *
	 * @return string
	 */
	public function construct_github_url(array $options): string {
		$url_with_path = $this->base_url . $this->path;

		if(!array_key_exists('access_token', $options))
			$options['access_token'] = $this->oauth_token;

		return static::construct_remote_url($url_with_path, $options);
	}

	/**
	 * Retrieves the parsed and filtered version data
	 *
	 * @param array $options
	 * @param int $minimum_major_version
	 * @param array $test_version
	 *
	 * @return array
	 */
	public function get_parsed_versions(
		array $options,
		int $minimum_major_version = 0,
		array $test_version = [VersionFilter::class, 'test_version']
	): array {
		$keep_traversing = true;
		$versions = [];

		if(!array_key_exists('page', $options))
			$options['page'] = 1;

		while($keep_traversing) {
			$data = $this->fetch_api_data($options);

			if(empty($data)) {
				$keep_traversing = false;
			} else {
				$versions = VersionFilter::parse_versions($data, $minimum_major_version, $versions, $test_version);
			}

			$options['page']++;
		}

		return $versions;
	}
}