<?php
/**
 * EmailTestCase
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License 
 */
namespace Actum\Utils;

class EmailTestCase extends \PHPUnit_Framework_TestCase {

	private $helper;
	private $hasInternet;

	public function provideEmails() {
		$provide = array();
		foreach ($this->getHelper()->getTestCases() as $testCase) {
			$provide[ (string) $testCase->attributes()->id] = $this->createTestCase($testCase);
		}
		return $provide;
	}

	private function createTestCase(\SimpleXMLElement $testCase) {
		$expected = $this->getHelper()
			->getConstant($testCase->diagnosis, preg_replace('#Test$#', '', get_class($this)));
		if ( ! $this->hasInternet() && $expected <= Email::ISEMAIL_DNSWARN) {
			$expected = Email::ISEMAIL_VALID;
		}
		return array(
			(string) $testCase->address,
			$this->hasInternet(),
			$expected,
			$testCase->comment,
		);
	}

	protected function getHelper() {
		if ($this->helper === NULL) {
			$this->helper = new EmailTestHelper;
		}
		return $this->helper;
	}

	protected function hasInternet() {
		if ($this->hasInternet === NULL) {
			// The @ operator is used here to avoid DNS errors when there is no connection.
			$sock = @fsockopen("www.google.com", 80, $errno, $errstr, 1);
			$this->hasInternet = (bool) $sock ? TRUE : FALSE;
		}
		return $this->hasInternet;
	}
}