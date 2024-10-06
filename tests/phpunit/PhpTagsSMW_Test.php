<?php
namespace PhpTags;

/**
 * @covers \PhpTags\SMWExtSQI
 */
class PhpTagsSMW_Test extends \PHPUnit\Framework\TestCase {

	public function testRun_constant_1() {
		if ( Renderer::$needInitRuntime ) {
			\MediaWiki\MediaWikiServices::getInstance()->getHookContainer()->run( 'PhpTagsRuntimeFirstInit' );
			Hooks::loadData();
			Runtime::$loopsLimit = 1000;
			Renderer::$needInitRuntime = false;
		}
		$this->assertEquals(
			Runtime::runSource( 'echo PHPTAGS_SMW_VERSION;' ),
			[ \ExtensionRegistry::getInstance()->getAllThings()['PhpTags SMW']['version'] ]
		);
	}

}
