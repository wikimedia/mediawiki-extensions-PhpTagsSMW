<?php
namespace PhpTagsObjects;

/**
 * Description of SMWExtSQI
 *
 * @author pastakhov
 */
class SMWExtSQI extends \PhpTags\GenericObject {

	public function m___construct( $config = null ) {
		if ( false === class_exists( '\\SQI\\SemanticQueryInterface' ) ) {
			throw new \Exception( wfMessage( 'phptagssmw-ext-sqi-not-installed' )->text() );
		}
		$this->value = new \SQI\SemanticQueryInterface( $config );
		return true;
	}

	/**
	 *
	 * @return \SQI\SemanticQueryInterface
	 */
	private function getSQI() {
		return $this->value;
	}

	public function m_from( $page, $flatResult = false ) {
		$this->getSQI()->from( $page, (bool)$flatResult );
		return $this;
	}

	public function m_condition( $condition, $conditionValue = null ) {
		$this->getSQI()->condition( $condition, $conditionValue );
		return $this;
	}

	public function m_category( $category ) {
		$this->getSQI()->category( $category );
		return $this;
	}

	public function m_printout( $printout ) {
		$this->getSQI()->printout( $printout );
		return $this;
	}

	public function m_limit( $limit ) {
		$this->getSQI()->limit( abs( $limit ) );
		return $this;
	}

	public function m_offset( $offset ) {
		$this->getSQI()->offset( abs( $offset ) );
		return $this;
	}

	public function m_count() {
		return $this->getSQI()->count();
	}

	public function m_toArray( $stringifyPropValues = false ) {
		return $this->getSQI()->toArray( (bool)$stringifyPropValues );
	}

}
