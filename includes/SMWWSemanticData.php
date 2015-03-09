<?php
namespace PhpTagsObjects;

/**
 * Class with static methods for affecting how semantic data is handled
 * for the page currently being parsed by MediaWiki.
 *
 * These methods can for example be called through a parser function in
 * some extension. Uses include:
 * - Adding semantic property values to the page being parsed.
 *
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
class SMWWSemanticData extends \PhpTags\GenericObject {

	/**
	 * DataValue instances to add to the SemanticData for the
	 * current page being parsed.
	 */
	private static $addedDataValues = array();

	/**
	 * Move any added semantic property values to the passed
	 * SemanticData instance.
	 *
	 * This is called in the PhpTagsSMWHooks implementation for
	 * "SMWStore::updateDataBefore", in order to move the data to the
	 * container that SMW will use to write property values for the
	 * page that has just been parsed.
	 */
	public static function moveDataToContainer( \SMW\SemanticData $semanticData ) {
		foreach ( self::$addedDataValues as $dv ) {
			$semanticData->addDataValue( $dv );
		}

		self::$addedDataValues = array();
	}

	/**
	 * Construct a value entry from a pair of strings. The property
	 * string can be an ID, label, or page name (without the
	 * namespace) for a property. The value string is parsed using an
	 * instance of the appropriate DataValue class.
	 *
	 * @param string $propertyName Property label, ID, or page name
	 * @param string $valueString
	 * @return SemanticValue
	 */
	private static function makeDataValueFromStrings( $propertyName,
			$valueString ) {
		$property = SMWWSemanticProperty::makeDataItem( $propertyName );
		$dataValue = \SMW\DataValueFactory::newPropertyObjectValue(
				$property, $valueString );
		return $dataValue;
	}

/*
 * PhpTags object
 */

	public static function s_addValue( $propertyName, $valueString ) {
		self::$addedDataValues[] = self::makeDataValueFromStrings(
				$propertyName, $valueString );
	}

}
