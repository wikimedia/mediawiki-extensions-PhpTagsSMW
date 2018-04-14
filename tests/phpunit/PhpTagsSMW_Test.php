<?php
namespace PhpTags;

class PhpTagsSMW_Test extends \PHPUnit\Framework\TestCase {

	public function testRun_constant_1() {
		$this->assertEquals(
				Runtime::runSource( 'echo PHPTAGS_SMW_VERSION;' ),
				[ \ExtensionRegistry::getInstance()->getAllThings()['PhpTags SMW']['version'] ]
			);
	}

}
