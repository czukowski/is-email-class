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

	private $hasInternet;
	private $metaHelper;

	/**
	 * @dataProvider  provideEmails
	 */
	public function testIsEmail($email, $checkDns, $diagnosis, $expected, $comment) {
		$actual = $this->isEmail($email, $checkDns);
		if ($actual !== $expected) {
			$comment = $this->getMessage($email, $diagnosis, NULL, $comment);
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
		$testsXml = new EmailTestXml($this, $this->getXmlPath('tests'));
		return $testsXml->getTestCases();
	}

	/**
	 * Formats message for output
	 */
	protected function getMessage($email, $expectedCode, $actualCode, $comment) {
		$actual = $this->getAnalysis($actualCode);
		$expected = $this->getAnalysis($expectedCode);
		$result = array(
			str_replace(array("\n", "\r", "\t"), array('↓', '↓', '→'), $email),
			str_repeat('-', mb_strlen($email, 'utf-8')),
			($expectedCode === NULL ? '' : 'Expected: '.$expected['description'].' ('.$expected['id'].')'),
			($actualCode === NULL ? '' : 'Actual: '.$actual['description'].' ('.$actual['id'].')'),
			$comment,
		);
		return implode("\n", array_filter($result));
	}

	/**
	 * @return  boolean
	 */
	public function hasInternet() {
		if ($this->hasInternet === NULL) {
			// The @ operator is used here to avoid DNS errors when there is no connection.
			$sock = @fsockopen("www.google.com", 80, $errno, $errstr, 1);
			$this->hasInternet = (bool) $sock ? TRUE : FALSE;
		}
		return $this->hasInternet;
	}

	/**
	 * @return  EmailTestHelper 
	 */
	protected function getMetaHelper() {
		if ($this->metaHelper === NULL) {
			$this->metaHelper = new EmailMetaXml($this->getXmlPath('meta'));
		}
		return $this->metaHelper;
	}

	/**
	 * @param   mixed  $diagnosis
	 * @return  array 
	 */
	public function getAnalysis($diagnosis) {
		return $this->getMetaHelper()
			->getAnalysis($diagnosis);
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

	/**
	 * @param   string  $file
	 * @return  string
	 */
	private function getXmlPath($file) {
		return realpath(__DIR__.'/xml/'.$file.'.xml');
	}
}