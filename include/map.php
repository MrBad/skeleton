<?php

	/**
	 * Pseudo rewrite - TODO - implement RewriteMap
	 */

	$RewriteRules = [
		[
			'rule' => "{^/someurl}i",
			'replace' => "/somecontroller/someaction/1/"
		],
	];
