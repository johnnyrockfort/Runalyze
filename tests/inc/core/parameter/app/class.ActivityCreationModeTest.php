<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-09-19 at 18:59:40.
 */
class ActivityCreationModeTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ActivityCreationMode
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ActivityCreationMode;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers ActivityCreationMode::usesUpload
	 * @covers ActivityCreationMode::usesGarminCommunicator
	 * @covers ActivityCreationMode::usesForm
	 */
	public function testModes() {
		$this->object->set( ActivityCreationMode::UPLOAD );
		$this->assertEquals(true, $this->object->usesUpload());
		$this->assertEquals(false, $this->object->usesGarminCommunicator());
		$this->assertEquals(false, $this->object->usesForm());

		$this->object->set( ActivityCreationMode::GARMIN );
		$this->assertEquals(false, $this->object->usesUpload());
		$this->assertEquals(true, $this->object->usesGarminCommunicator());
		$this->assertEquals(false, $this->object->usesForm());

		$this->object->set( ActivityCreationMode::FORM );
		$this->assertEquals(false, $this->object->usesUpload());
		$this->assertEquals(false, $this->object->usesGarminCommunicator());
		$this->assertEquals(true, $this->object->usesForm());
	}

}
