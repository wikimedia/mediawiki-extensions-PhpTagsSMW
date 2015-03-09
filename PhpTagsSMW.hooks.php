<?php

/**
 * Contains the implementations of hooks used by PhpTags SMW.
 *
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

	/**
	 * Called before SemanticData is updated in the SMW database,
	 * allowing adding or changing data.
	 *
	 * Used to add property values to those stored for the current
	 * page. Such property values can be specified using the
	 * SemanticProcessing class.
	 *
	 * @return boolean Always true unless hook processing must stop
	 */
	public static function onSMWUpdateDataBefore( \SMW\Store $store,
			\SMW\SemanticData $semanticData ) {
		// Move any data added for the current page to storage
		\PhpTagsObjects\SMWWSemanticData::moveDataToContainer( $semanticData );

		return true;
	}

}

