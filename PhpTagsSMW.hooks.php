<?php

/**
 * Contains the implementations of hooks used by PhpTags SMW.
 *
 * @file PhpTagsSMW.hooks.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
class PhpTagsSMWHooks {

	/**
	 * Called when the PhpTags runtime initializes for the first time.
	 *
	 * Used for initialization of things that are only used when
	 * PhpTags is.
	 */
	public static function onPhpTagsRuntimeFirstInit() {
		\PhpTags\Hooks::addJsonFile( __DIR__ . '/PhpTagsSMW.json' );
		return true;
	}

}
