<?php

namespace App\Helpers\Basic;


/**
 * Filter helper class that holds the result of a regex match on whether or not the match was successful and what it
 * matched
 *
 * @package App\Helpers\Filters
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class VersionMatched {
	/**
	 * @var bool
	 */
	public $matched = false;

	/**
	 * @var string
	 */
	public $matching = '';

	/**
	 * VersionMatched constructor.
	 *
	 * @param bool $matched
	 * @param string $matching
	 */
	public function __construct(bool $matched, string $matching) {
		$this->matched = $matched;
		$this->matching = $matching;
	}
}