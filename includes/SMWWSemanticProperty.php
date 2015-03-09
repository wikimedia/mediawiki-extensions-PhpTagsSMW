<?php
namespace PhpTagsObjects;

/**
 * Static methods for dealing with properties, their representations,
 * and basic data access for individual properties and property values.
 *
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
class SMWWSemanticProperty extends \PhpTags\GenericObject {

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
	 * Return property ID corresponding to the given name. The name
	 * can be either one of:
	 * - A label for a predefined ("special") property. The
	 *   corresponding ID will be returned.
	 * - A name for a user-defined property, i.e. a property page
	 *   name. (The namespace should not be included.) It will be
	 *   returned normalized to DBKey form.
	 * - A predefined ("special") property ID, which will be returned
	 *   unchanged.
	 * .
	 * If the name does not begin with an underscore, it is
	 * normalized before looking up the ID. (Trimming whitespace,
	 * replacing underscores with spaces, and uppercasing the first
	 * character if property page names are case-insensitive.)
	 *
	 * If the name is not a label or an ID for a predefined property,
	 * the name will be assumed to be a valid page name, and returned
	 * with any spaces replaced by underscores.
	 *
	 * @param string $name The property name
	 * @return string      The property ID
	 */
	public static function getIdForName( $name ) {
		$name = trim( $name );
		if ( $name === '' ) {
			return '';
		}
		// Also attempt to look up IDs for strings that begin
		// with an underscore. There are such labels and aliases
		// for special properties, e.g. in SESP.
		if ( $name[0] === '_' ) {
			// normalize to DbKey form
			$name = str_replace( ' ', '_', $name );
		} else {
			// normalize to title string form
			$name = self::capitalizeName( $name );
			$name = str_replace( '_', ' ', $name );
		}
		$id = \SMWDIProperty::findPropertyID( $name );
		if ( $id !== false ) {
			return $id;
		}
		return str_replace( ' ', '_', $name );
	}

	/**
	 * Return property name corresponding to the given ID. The ID is
	 * assumed to be valid; apart from trimming whitespace, no
	 * correction is attempted.
	 *
	 * If no pre-defined property label is found, the ID is assumed to
	 * be a page name, and is returned normalized - unless it begins
	 * with an underscore, in which case it is assumed to be a
	 * nameless property, and is returned unchanged.
	 *
	 * @param string $id The property ID
	 * @return string    The property name
	 */
	public static function getNameForId( $id ) {
		$id = trim( $id );
		if ( $id === '' ) {
			return '';
		}
		$name = \SMWDIProperty::findPropertyLabel( $id );
		if ( $name !== '' ) {
			return $name;
		}
		if ( $id[0] === '_' ) {
			return $id;
		}
		$id = self::capitalizeName( $id );
		return str_replace( '_', ' ', $id );
	}

	/**
	 * Make a SMWDIProperty instance for the given property name.
	 *
	 * This can throw an exception if SMW detects that the property
	 * name (or the ID it translates into) is invalid.
	 *
	 * @param string $name The property name
	 * @return SMWDIProperty
	 */
	public static function makeDataItem( $name ) {
		$id = self::getIdForName( $name );
		return new \SMWDIProperty( $id );
	}

/*
 * PhpTags object
 */

	public static function s_getIdForName( $name ) {
		return self::getIdForName( $name );
	}

	public static function s_getNameForId( $id ) {
		return self::getNameForId( $id );
	}

}
