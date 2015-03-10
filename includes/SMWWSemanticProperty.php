<?php
namespace PhpTagsObjects;

/**
 * Static methods for dealing with properties, their representations,
 * and basic data access for individual properties and property values.
 *
 * @file SMWWSemanticProperty.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
class SMWWSemanticProperty extends \PhpTags\GenericObject {

	/**
	 * Remove the property namespace prefix if present in the given
	 * string. Looks for both the canonical and localized versions of
	 * the prefix. The check is case-insensitive.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function stripPropertyNamespace( $name ) {
		global $wgContLang;

		$colonPos = strpos( $name, ':' );
		if ( $colonPos === false ) {
			// Nothing to do
			return $name;
		}
		$prefix = substr( $name, 0, $colonPos );
		$prefix = str_replace( ' ', '_', $prefix );
		$nsIndex = $wgContLang->getNsIndex( $prefix );
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
		global $wgContLang;

		if ( \MWNamespace::isCapitalized( SMW_NS_PROPERTY ) ) {
			$name = $wgContLang->ucfirst( $name );
		}
		return $name;
	}

	/**
	 * Check whether the predefined property given by an ID exists.
	 * The ID must be normalized.
	 *
	 * @param string $id
	 * @return boolean
	 */
	private static function isValidPredefined( $id ) {
		return \SMWDIProperty::getPredefinedPropertyTypeId( $id ) !== '';
	}

	/**
	 * Return property ID corresponding to the given name, if the ID
	 * exists. The name can be either one of:
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
	 * @param string $name
	 * @return string The property ID, or an empty string
	 */
	public static function getIdForName( $name ) {
		$name = trim( $name );
		$name = self::stripPropertyNamespace( $name );
		if ( $name === '' ) {
			return '';
		}
		if ( $name[0] === '_' ) {
			// Predefined property ID given - normalize to
			// DbKey form and check if the property exists
			$id = str_replace( ' ', '_', $name );
			if ( self::isValidPredefined( $id ) === true ) {
				return $id;
			}
			return '';
		}
		// Normalize to title string form and lookup
		$name = self::capitalizeName( $name );
		$name = str_replace( '_', ' ', $name );
		$id = \SMWDIProperty::findPropertyID( $name );
		if ( $id !== false ) {
			return $id;
		}
		// SMW considers it user-defined - a property page name
		return str_replace( ' ', '_', $name );
	}

	/**
	 * Return property name corresponding to the given ID, if the ID
	 * is valid. The ID can be:
	 * - A predefined ("special") property ID. The label for the
	 *   property will be returned if it exists. If not, then as an
	 *   optional fallback (the default), the ID can be returned
	 *   (normalized as an ID) instead of an empty string.
	 * - A page name. It will be returned normalized, with spaces
	 *   between words instead of underscores.
	 * .
	 * Before look-up, the ID is normalized. Whitespace is trimmed,
	 * and the property namespace prefix is removed if present, before
	 * the ID is considered. Both spaces and underscores are accepted
	 * between words, and the first character is capitalized
	 * if property page names are case-insensitive.
	 *
	 * For an invalid ID, as well as for a string that is empty or
	 * only consists of whitespace, an empty string will be returned.
	 *
	 * @param string $id
	 * @param boolean $getIdIfNameless
	 * @return string The property name, or an empty string
	 */
	public static function getNameForId( $id, $getIdIfNameless = true ) {
		$id = trim( $id );
		$id = self::stripPropertyNamespace( $id );
		if ( $id === '' ) {
			return '';
		}
		if ( $id[0] === '_' ) {
			$id = str_replace( ' ', '_', $id );
			$name = \SMWDIProperty::findPropertyLabel( $id );
			if ( $name !== '' ) {
				return $name;
			}
			// No label found; optionally, if the ID is valid,
			// return it as a fallback
			if ( $getIdIfNameless === true &&
					self::isValidPredefined( $id ) ) {
				return $id;
			}
			// Invalid ID (or fallback not used)
			return '';
		}
		$id = self::capitalizeName( $id );
		return str_replace( '_', ' ', $id );
	}

	/**
	 * Normalize the given property string representation.
	 *
	 * Whitespace is trimmed, and the property namespace prefix is
	 * removed if present. Unless the name begins with an underscore,
	 * underscores are turned into spaces, and the first character is
	 * capitalized if property page names are case-insensitive. (If
	 * the name begins with an underscore, spaces are instead replaced
	 * by underscores following trimming and prefix removal.)
	 *
	 * @param string $nameOrId
	 * @return string
	 */
	public static function normalize( $nameOrId ) {
		$nameOrId = trim( $nameOrId );
		$nameOrId = self::stripPropertyNamespace( $nameOrId );
		if ( $nameOrId === '' ) {
			return '';
		}
		if ( $nameOrId[0] === '_' ) {
			return str_replace( ' ', '_', $nameOrId );
		} else {
			$nameOrId = self::capitalizeName( $nameOrId );
			return str_replace( '_', ' ', $nameOrId );
		}
	}

	/**
	 * Make a SMWDIProperty instance for the given property name.
	 *
	 * Throws a PhpTags HookException if it is detected that the
	 * property name is invalid.
	 *
	 * @param string $name The property name
	 * @return \SMWDIProperty
	 * @throws \PhpTags\HookException
	 */
	public static function makeDataItem( $name ) {
		$id = self::getIdForName( $name );
		try {
			$dataItem = new \SMWDIProperty( $id );
		} catch ( \SMWDataItemException $e ) {
			$normName = self::normalize( $name );
			$message = wfMessage( 'smw_noproperty', $normName )->inContentLanguage()->text();
			throw new \PhpTags\HookException( \PhpTags\HookException::EXCEPTION_FATAL, $message );
		}
		return $dataItem;
	}

/*
 * PhpTags object
 */

	public static function s_getIdForName( $name ) {
		return self::getIdForName( $name );
	}

	public static function s_getNameForId( $id, $getIdIfNameless = true ) {
		return self::getNameForId( $id, $getIdIfNameless );
	}

	public static function s_normalize( $nameOrId ) {
		return self::normalize( $nameOrId );
	}

}
