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
	private $hasInternet;

	public function provideEmails() {
		$provide = array();
		foreach ($this->getHelper()->getTestCases() as $testCase) {
			$provide[ (string) $testCase->attributes()->id] = $this->createTestCase($testCase);
		}
		return $provide;
	}

	private function createTestCase(\SimpleXMLElement $testCase) {
		$isValid = $this->getExpectedDiagnosis( (string) $testCase->diagnosis);
		if ( ! $this->hasInternet() && $isValid <= Email::ISEMAIL_DNSWARN) {
			$isValid = Email::ISEMAIL_VALID;
		}
		$formallyValid = $isValid <= Email::ISEMAIL_RFC5321_IPV6DEPRECATED;
		return array(
			(string) $testCase->address,
			$this->hasInternet(),
			$isValid,
			$formallyValid,
			(string) $testCase->comment,
		);
	}

	protected function getExpectedDiagnosis($constantName) {
		$diagnosis = $this->getHelper()
			->getAnalysis($constantName);
		return $diagnosis['value'];
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
     * Returns domain part fro mthe email address
     * 
     * @param   string  $email
     * @return  string
     */
    private function getEmailDomain($email) {
        return substr($email, strrpos($email, '@') + 1);
    }
}