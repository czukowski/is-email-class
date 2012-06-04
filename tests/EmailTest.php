<?php
/**
 * Email test
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
	public function testIsEmail($email, $checkDns, $expected, $unused, $comment) {
		$actual = Email::is_email($email, $checkDns, TRUE);
		if ($actual !== $expected) {
			$comment = $this->getHelper()
				->getMessage($email, $expected, $actual, $comment);
		}
		$this->assertEquals($expected, $actual, $comment);
	}
}
