<?php

namespace App\Helpers\Remote;

/**
 * Base remote helper class used to retrieve json and other types of data used for various things
 *
 * @package App\Helpers
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class Remote {
	/**
	 * @param string $url
	 * @param array $opts
	 * @param bool $use_include_path
	 *
	 * @return array
	 */
	public static function fetch_remote_json(
		string $url,
		array $opts = [
			'http' => [
				'method' => 'GET',
				'header' => [
					'User-Agent: PHP'
				]
			]
		],
		bool $use_include_path = false
	): array {
		// Create the stream context with the appropriate headers
		$context = stream_context_create($opts);

		// Return the decoded json
		return json_decode(file_get_contents($url, $use_include_path, $context));
	}

	/**
	 * Constructs a url with get parameters
	 *
	 * @param string $url
	 * @param array $options
	 *
	 * @return string
	 */
	public static function construct_remote_url(string $url, array $options): string {
		// If the options are empty, just return the naked url
		if(empty($options))
			return $url;

		// Append a ? mark to the combined base/path url
		$url .= '?';

		array_walk($options, function($value, $key) use (&$url) {
			// Check for first value so we know if we need to add a & or not to the get params
			$first = substr($url, -1) == '?';

			// If not the first value add a & before the key/value get pair
			if(!$first)
				$url .= '&';

			// Append the key/value pair to the get url
			$url .= "{$key}={$value}";
		});

		return $url;
	}
}