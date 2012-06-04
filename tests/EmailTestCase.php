<?php
/**
 * EmailTestCase
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License 
 */
namespace Actum\Utils;

abstract class EmailTestCase extends \PHPUnit_Framework_TestCase {

	private $helper;

	/**
	 * @dataProvider  provideEmails
	 */
	public function testIsEmail($email, $checkDns, $diagnosis, $expected, $comment) {
		$actual = $this->isEmail($email, $checkDns);
		if ($actual !== $expected) {
			$comment = $this->getHelper()
				->getMessage($email, $diagnosis, NULL, $comment);
		}
		$this->assertEquals($expected, $actual, $comment);
	}

	/**
	 * @throws  \LogicException 
	 */
	protected function isEmail($email) {
		throw new \LogicException('Subclasses using the default test must implement their own isEmail method');
	}

	/**
	 * Provides test cases from XML files
	 */
	public function provideEmails() {
		return $this->getHelper()
			->getTestCases('xml/tests.xml');
	}

	/**
	 * @return  EmailTestHelper 
	 */
	protected function getHelper() {
		if ($this->helper === NULL) {
			$this->helper = new EmailTestHelper('xml/meta.xml');
		}
		return $this->helper;
	}

    /**
     * Validates domain accessibility using DNS records check
     * 
     * @param   string  $email
     * @return  boolean
     */
    protected function isValidEmailDomain($email) {
        return (checkdnsrr($this->getEmailDomain($email), 'MX'));
    }

    /**
     * Returns domain part from the email address
     * 
     * @param   string  $email
     * @return  string
     */
    private function getEmailDomain($email) {
        return substr($email, strrpos($email, '@') + 1);
    }
}