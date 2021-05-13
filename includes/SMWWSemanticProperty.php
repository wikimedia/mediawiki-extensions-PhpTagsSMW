<?php
namespace PhpTagsObjects;

use MediaWiki\MediaWikiServices;

/**
 * Static methods for dealing with properties, their representations,
 * and basic data access for individual properties and property values.
 *
 * @file SMWWSemanticProperty.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @license GPL-2.0-or-later
 */
class SMWWSemanticProperty extends \PhpTags\GenericObject {

	/**
	 * Extra aliases for predefined ("special") property IDs. (These
	 * are only used when looking up IDs - they are not used as labels
	 * when looking up names.)
	 *
	 * @var array $extraAliases Array of string => string entries
	 */
	private static $extraAliases = [
		// Recognized by SMW's #subobject parser function. In
		// PhpTags SMW, simply recognize it universally instead of
		// making subobjects a special case.
		'@sortkey' => '_SKEY'
	];

	/**
	 * Remove the property namespace prefix if present in the given
	 * string. Looks for both the canonical and localized versions of
	 * the prefix. The check is case-insensitive.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function stripPropertyNamespace( $name ) {
		$colonPos = strpos( $name, ':' );
		if ( $colonPos === false ) {
			// Nothing to do
			return $name;
		}
		$prefix = substr( $name, 0, $colonPos );
		$prefix = str_replace( ' ', '_', $prefix );
		$nsIndex = MediaWikiServices::getInstance()->getContentLanguage()->getNsIndex( $prefix );
		if ( $nsIndex === SMW_NS_PROPERTY ) {
			$name = substr( $name, $colonPos + 1 );
			return ( $name !== false ) ? $name : '';
		}
		// Other name with a ':'
		return $name;
	}

	/**
	 * Uppercase the first character of the given property name
	 * if the property namespace is case-insensitive.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function capitalizeName( $name ) {
		if ( \MWNamespace::isCapitalized( SMW_NS_PROPERTY ) ) {
			$name = MediaWikiServices::getInstance()->getContentLanguage()->ucfirst( $name );
		}
		return $name;
	}

	/**
	 * Normalize the given property string. Returns a "cleaned up"
	 * copy suitable for printing, and also for doing a name <-> ID
	 * lookup.
	 *
	 * Whitespace is trimmed, and the property namespace prefix is
	 * removed if present. Unless the name begins with an underscore,
	 * underscores are turned into spaces (and the first character is
	 * capitalized, if property page names are case-insensitive). If
	 * the name begins with an underscore, spaces are instead replaced
	 * by underscores following trimming and prefix removal.
	 *
	 * @param string $property
	 * @return string
	 */
	public static function normalize( $property ) {
		$property = trim( $property );
		$property = self::stripPropertyNamespace( $property );
		if ( $property === '' ) {
			return '';
		}
		if ( $property[0] === '_' ) {
			return str_replace( ' ', '_', $property );
		} else {
			$property = self::capitalizeName( $property );
			return str_replace( '_', ' ', $property );
		}
	}

	/**
	 * Return predefined/special property ID for a normalized property
	 * label or alias. If none is found, false will be returned.
	 *
	 * @param string $name Normalized property label or alias
	 * @return string|false The property ID, or false
	 */
	public static function getIdForLabelOrAlias( $name ) {
		$registry = \SMW\PropertyRegistry::getInstance();
		$id = $registry->findPropertyIdByLabel( $name );
		if ( $id !== false ) {
			return $id;
		}
		// Check extra aliases
		if ( isset( self::$extraAliases[$name] ) ) {
			return self::$extraAliases[$name];
		}
		return false;
	}

	/**
	 * Return property label for a normalized predefined/special
	 * property ID. If none is found, false will be returned.
	 *
	 * @param string $id Normalized property id
	 * @return string|false The property label, or false
	 */
	public static function getLabelForId( $id ) {
		$registry = \SMW\PropertyRegistry::getInstance();
		$label = $registry->findPropertyLabelById( $id );
		if ( $label !== '' ) {
			return $label;
		}
		return false;
	}

	/**
	 * Check whether the given predefined/special property ID is
	 * valid. The string must be normalized.
	 *
	 * @param string $id Normalized predefined/special property ID
	 * @return bool
	 */
	public static function isValidPredefinedId( $id ) {
		$registry = \SMW\PropertyRegistry::getInstance();
		return $registry->isKnownPropertyId( $id );
	}

	/**
	 * Return property ID for the given property string, if a valid
	 * property. The property string can be:
	 * - A label or alias for a predefined ("special") property. The
	 *   corresponding ID will be returned.
	 * - Any other name not beginning with an underscore is regarded
	 *   as a user-defined property, i.e. a property page name. It
	 *   will be returned normalized to DbKey form.
	 * - A name beginning with an underscore is regarded as an ID for
	 *   a predefined ("special") property. If the property exists,
	 *   the ID will be returned normalized. If it doesn't exist, an
	 *   empty string will instead be returned.
	 * .
	 * Before look-up, the name is normalized. Whitespace is trimmed,
	 * and the property namespace prefix is removed if present, before
	 * the name is considered. Both spaces and underscores are
	 * accepted between words, and the first character is capitalized
	 * if property page names are case-insensitive.
	 *
	 * For a string that is empty or only consists of whitespace, an
	 * empty string will be returned.
	 *
	 * @param string $property
	 * @return string The property ID, or an empty string
	 */
	public static function findId( $property ) {
		$property = self::normalize( $property );
		if ( $property === '' ) {
			return '';
		}
		if ( $property[0] === '_' ) {
			// Predefined property ID given - check if the
			// property exists
			if ( self::isValidPredefinedId( $property ) ) {
				return $property;
			}
			return '';
		}
		$id = self::getIdForLabelOrAlias( $property );
		if ( $id !== false ) {
			return $id;
		}
		// User-defined, i.e. a property page name
		return str_replace( ' ', '_', $property );
	}

	/**
	 * Return property name for the given property string, if a valid
	 * property and a name exists. The property string can be:
	 * - A predefined ("special") property ID. The label for the
	 *   property will be returned if the property exists and has
	 *   one. For a nameless property, as an optional fallback the ID
	 *   can be returned (normalized as an ID) instead of an empty
	 *   string.
	 * - The label or an alias for a predefined ("special") property.
	 *   The "main" label will be returned.
	 * - A page name. It will be returned normalized, with spaces
	 *   between words instead of underscores.
	 * .
	 * Before look-up, the property string is normalized. Whitespace
	 * is trimmed, and the property namespace prefix is removed if
	 * present. Both spaces and underscores are accepted between
	 * words, and the first character is capitalized if property page
	 * names are case-insensitive.
	 *
	 * An empty string will be returned for:
	 * - An invalid ID.
	 * - An ID for a nameless predefined/special property, unless
	 *   $getIdIfNameless is true.
	 * - An empty string, or a string that only contains whitespace.
	 *
	 * @param string $property
	 * @param bool $getIdIfNameless
	 * @return string The property name, or an empty string
	 */
	public static function findName( $property, $getIdIfNameless = false ) {
		$property = self::normalize( $property );
		if ( $property === '' ) {
			return '';
		}
		if ( $property[0] === '_' ) {
			$label = self::getLabelForId( $property );
			if ( $label !== false ) {
				return $label;
			}
			// No label found; optionally, if the ID is valid,
			// return it as a fallback
			if ( $getIdIfNameless === true && self::isValidPredefinedId( $property ) ) {
				return $property;
			}
			// Invalid ID (or fallback not used)
			return '';
		}
		// In case it is an alias for a label, do a label lookup
		$id = self::getIdForLabelOrAlias( $property );
		if ( $id !== false ) {
			$label = self::getLabelForId( $id );
			if ( $label !== false ) {
				return $label;
			}
		}
		// User-defined, i.e. a property page name
		return $property;
	}

/*
 * PhpTags object
 */

	public static function s_normalize( $property ) {
		return self::normalize( $property );
	}

	public static function s_findId( $property ) {
		return self::findId( $property );
	}

	public static function s_findName( $property, $getIdIfNameless = false ) {
		return self::findName( $property, $getIdIfNameless );
	}

	/**
	 * @deprecated Remove in v1.5.0
	 */
	public static function s_getIdForName( $name ) {
		return self::findId( $name );
	}

	/**
	 * @deprecated Remove in v1.5.0
	 */
	public static function s_getNameForId( $id, $getIdIfNameless = true ) {
		return self::findName( $id, $getIdIfNameless );
	}

}
