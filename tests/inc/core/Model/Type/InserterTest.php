<?php

namespace Runalyze\Model\Type;

use PDO;

class InvalidInserterObjectForType_MockTester extends \Runalyze\Model\Entity {
	public function properties() {
		return array('foo');
	}
}

/**
 * Generated by hand
 */
class InserterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PDO
	 */
	protected $PDO;

	protected function setUp() {
		$this->PDO = new PDO('sqlite::memory:');
		$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->PDO->exec('CREATE TABLE IF NOT EXISTS `'.PREFIX.'type` (
			`id` INTEGER PRIMARY KEY AUTOINCREMENT,
			`name` VARCHAR(50) NOT NULL,
			`abbr` VARCHAR(5) NOT NULL,
			`sportid` INTEGER NOT NULL,
			`short` INTEGER NOT NULL,
			`hr_avg` SMALLINT NOT NULL,
			`quality_session` SMALLINT NOT NULL,
			`accountid` INTEGER NOT NULL
			);
		');
	}

	protected function tearDown() {
		$this->PDO->exec('DROP TABLE `'.PREFIX.'type`');
	}

	public function testWrongObject() {
	    if (PHP_MAJOR_VERSION >= 7) $this->setExpectedException('TypeError'); else $this->setExpectedException('\PHPUnit_Framework_Error');
		new Inserter($this->PDO, new InvalidInserterObjectForType_MockTester);
	}

	public function testSimpleInsert() {
		$Type = new Entity(array(
			Entity::NAME => 'Type name',
			Entity::ABBREVIATION => 'Tn',
			Entity::SPORTID => 1,
			Entity::SHORT => 0,
			Entity::HR_AVG => 120,
			Entity::QUALITY_SESSION => 1
		));

		$Inserter = new Inserter($this->PDO, $Type);
		$Inserter->setAccountID(1);
		$Inserter->insert();

		$data = $this->PDO->query('SELECT * FROM `'.PREFIX.'type` WHERE `accountid`=1')->fetch(PDO::FETCH_ASSOC);
		$New = new Entity($data);

		$this->assertEquals('Type name', $New->name());
		$this->assertEquals('Tn', $New->abbreviation());
		$this->assertEquals(1, $New->sportid());
		$this->assertEquals(120, $New->hrAvg());
		$this->assertEquals(true, $New->isQualitySession());
		$this->assertFalse($New->usesShortMode());
	}

}
