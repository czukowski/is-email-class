<?php
/**
 * Email test
 * 
 * Failing tests (for now):
 * 5, 58, 88, 89, 115, 116, 128, 129, 130, 131, 134, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147,
 * 148, 149, 150, 151, 152, 153, 154, 155, 156.
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class EmailTest extends EmailTestCase {

	/**
	 * @dataProvider  provideEmails
	 */
	public function testIsEmail($email, $checkDns, $expected, $comment) {
		$actual = Email::is_email($email, $checkDns, TRUE, $parsedata);
		if ($actual !== $expected) {
			$comment = $this->getMessage($email, $expected, $actual, $comment);
		}
		$this->assertEquals($expected, $actual, $comment);
	}

	private function getMessage($email, $expected, $actual, $comment) {
		$actual = $this->getHelper()
			->getAnalysis($actual);
		$expected = $this->getHelper()
			->getAnalysis($expected);
		return $email."\n"
			.str_repeat('-', mb_strlen($email, 'utf-8'))."\n"
			.'Expected: '.$expected['description']."\n"
			.'Actual: '.$actual['description']."\n"
			.$comment;
	}

	protected function getExpectedDiagnosis($constantName) {
		return $this->getHelper()
			->getConstant($constantName, preg_replace('#Test$#', '', get_class($this)));
	}
}
