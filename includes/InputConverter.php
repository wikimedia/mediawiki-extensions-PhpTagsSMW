<?php
namespace PhpTagsSMW;

use \PhpTagsObjects\SMWWSemanticProperty as WSemanticProperty;

/**
 * Class for handling conversion of user input, with type and other
 * validity checking.
 *
 * @file InputConverter.php
 * @ingroup PhpTagsSMW
 * @author Joel K. Pettersson <joelkpettersson@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */
class InputConverter {

	/**
	 * Added as a prefix for exception messages.
	 * @var string
	 */
	protected $callerName;

	/**
	 * Title instance for the page associated with the values being
	 * processed.
	 * @var \Title
	 */
	protected $title;

	/**
	 * DIWikiPage instance for the subject associated with the values
	 * being processed. Constructed on demand.
	 * @var \SMW\DIWikiPage
	 */
	protected $contextPage = null;

	/**
	 * Constructor. The caller name passed should correspond to what
	 * is visible to (and was used by) the PhpTags user.
	 *
	 * @param string $callerName
	 * @param \Title $title
	 */
	public function __construct( $callerName, \Title $title ) {
		$this->callerName = $callerName;
		$this->title = $title;
	}

	protected function getCallerPrefix() {
		return $this->callerName . ': ';
	}

	protected function getTitle() {
		return $this->title;
	}

	protected function getContextPage() {
		if ( $this->contextPage === null ) {
			$this->contextPage = \SMW\DIWikiPage::newFromTitle( $this->getTitle() );
		}
		return $this->contextPage;
	}

	protected function makeWarning( $message ) {
		$message = $this->getCallerPrefix() . $message;
		return new \PhpTags\HookException( \PhpTags\HookException::EXCEPTION_WARNING, $message );
	}

	/**
	 * Make a SMWDIProperty instance for a property given as a string.
	 * The property string can be a property label, ID, or page name.
	 *
	 * Throws a \PhpTags\HookException warning if it is detected that
	 * the property name is invalid.
	 *
	 * @param string $property Property label, ID, or page name
	 * @return \SMWDIProperty
	 * @throws \PhpTags\HookException
	 */
	public function makeDIProperty( $property ) {
		$id = WSemanticProperty::findId( $property );
		try {
			$dataItem = new \SMWDIProperty( $id );
		} catch ( \SMWDataItemException $e ) {
			$printName = WSemanticProperty::normalize( $property );
			$message = wfMessage( 'smw_noproperty', $printName )->inContentLanguage()->text();
			throw $this->makeWarning( $message );
		}
		return $dataItem;
	}

	/**
	 * Construct a DataValue for the given SMWDIProperty and
	 * value string. The string is parsed using an instance of
	 * the appropriate DataValue class.
	 *
	 * @param \SMWDIProperty $diProperty
	 * @param string $valueString
	 * @return \SMWDataValue
	 */
	public function makeDataValue( $diProperty, $valueString ) {
		$dataValue = \SMW\DataValueFactory::newPropertyObjectValue(
				$diProperty, $valueString, false, $this->getContextPage() );
		// @todo Throw warning for errors in DataValue?
		return $dataValue;
	}

	/**
	 * Convert a scalar value to a string compatible with DataValue
	 * classes. Returns null if the value is not scalar.
	 *
	 * @param scalar $value
	 * @return string|null
	 */
	protected function convertScalar( $value ) {
		if ( is_string( $value ) ) {
			return $value;
		}
		if ( is_bool( $value ) ) {
			// recognized by SMWBoolValue
			return $value ? "1" : "0";
		}
		if ( is_numeric( $value ) ) {
			return (string) $value;
		}
		return null;
	}

	/**
	 * Turn user input for a property value into a value string which
	 * can be used for making a DataValue.
	 *
	 * @param scalar|array $value Scalar or array of scalar|null
	 * @param boolean $allowRecord
	 * @return string
	 * @throws \PhpTags\HookException
	 */
	public function makeValueString( $value, $allowRecord = true ) {
		if ( $allowRecord === true && is_array( $value ) ) {
			$string = '';
			foreach ( $value as $subvalue ) {
				if ( $subvalue === null) {
					// Allow null for blank value
					$substring = '';
				} elseif ( ( $substring = $this->convertScalar( $subvalue ) ) === null ) {
					$message = 'property value must be scalar or array of scalar|null, array with entry of other type given';
					throw $this->makeWarning( $message );
				}
				// Escape value separator before adding
				$substring = str_replace( ';', '\;', $substring );
				if ( $string === '' ) {
					$string = $substring;
				} else {
					$string .= ';' . $substring;
				}
			}
			return $string;
		}
		$string = $this->convertScalar( $value );
		if ( $string === null ) {
			$message = ( $allowRecord === true ) ?
				'property value must be scalar or array of scalar|null, value given was neither scalar nor array' :
				'property value must be scalar, non-scalar given';
			throw $this->makeWarning( $message );
		}
		return $string;
	}

	/**
	 * Turn user array of property value assignments into a standard
	 * format.
	 *
	 * The input is an array which can contain the following kinds of
	 * entries:
	 * - Property string => array( value, ... ).
	 * - Property string => value. This is supported for convenience.
	 *   Note that record sub-values cannot be specified in an array
	 *   when using this format, because the array will be seen as a
	 *   list of independent values.
	 * .
	 * The output is an array of property => array( value string, ... )
	 * entries.
	 *
	 * When used to get an array for making a subobject,
	 * $linkbackProperty can be passed to add a named property holding
	 * the full name of the associated page.
	 *
	 * @param array $userArray
	 * @param string $linkbackProperty For use in subobjects
	 * @return array Array of property => array( value string, ... )
	 * @throws \PhpTags\HookException
	 */
	public function makeValueAssignmentArray( $userArray, $linkbackProperty = '' ) {
		$array = array();
		foreach ( $userArray as $key => $entry ) {
			if ( !is_string( $key ) ) {
				$message = 'value assignment array must contain entries with property string keys, array has entry with non-string key';
				throw $this->makeWarning( $message );
			}
			if ( !is_array( $entry ) ) {
				$entry = array( $entry );
			}
			foreach ( $entry as $value ) {
				$array[$key][] = $this->makeValueString( $value );
			}
		}
		$linkbackProperty = trim( $linkbackProperty );
		if ( $linkbackProperty !== '' ) {
			$fullPageName = $this->getTitle()->getPrefixedText();
			$array[$linkbackProperty][] = $fullPageName;
		}
		return $array;
	}

	/**
	 * Construct a subobject for the given standardized value
	 * assignment array.
	 *
	 * If $id is not an empty string, it will be used to set the
	 * identifier for the subobject. Otherwise, the identifier will
	 * be set to a hash, the same way it is in SMW.
	 *
	 * @param array $valueAssignments
	 * @param string $id Optional named identifier for subobject
	 * @return \SMW\Subobject
	 * @throws \PhpTags\HookException
	 */
	public function makeSubobject( $valueAssignments, $id = '' ) {
		$subobject = new \SMW\Subobject( $this->getTitle() );
		$id = trim( $id );
		$id = ( $id !== '' ) ?
			str_replace( ' ', '_', $id ) :
			\SMW\HashBuilder::createHashIdForContent( $valueAssignments, '_' );
		$subobject->setEmptyContainerForId( $id );
		foreach ( $valueAssignments as $property => $values ) {
			$diProperty = $this->makeDIProperty( $property );
			foreach ( $values as $valueString ) {
				$dataValue = $this->makeDataValue( $diProperty, $valueString );
				$subobject->addDataValue( $dataValue );
			}
		}
		return $subobject;
	}

}
