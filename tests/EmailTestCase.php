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
	private $replaceChars;
	private $searchChars;

	/**
	 * @dataProvider  provideEmails
	 */
	public function testIsEmail($email, $checkDns, $unused, $expected, $comment) {
		$actual = $this->isEmail($email, $checkDns);
		$this->assertEquals($expected, $actual, $comment);
	}

	protected function isEmail($email) {
		throw new \LogicException('Subclasses using the default test must implement their own isEmail method');
	}

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
			$this->getDecodedString($testCase->address),
			$this->hasInternet(),
			$isValid,
			$formallyValid,
			$this->getDecodedString($testCase->comment),
		);
	}

	/**
	 * Certain email address characters in XML files are encoded, this method will decode them
	 * 
	 * @param   mixed  $value
	 * @return  string
	 */
	private function getDecodedString($value) {
		return str_replace($this->getSearch(), $this->getReplace(), (string) $value);
	}

	/**
	 * Retrieves diagnosis by its ID (constant name)
	 * 
	 * @param   mixed   $constantName
	 * @return  integer
	 */
	protected function getExpectedDiagnosis($constantName) {
		$diagnosis = $this->getHelper()
			->getAnalysis($constantName);
		return $diagnosis['value'];
	}

	/**
	 * @return  EmailTestHelper 
	 */
	protected function getHelper() {
		if ($this->helper === NULL) {
			$this->helper = new EmailTestHelper;
		}
		return $this->helper;
	}

	/**
	 * @return  boolean
	 */
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
     * Returns domain part from the email address
     * 
     * @param   string  $email
     * @return  string
     */
    private function getEmailDomain($email) {
        return substr($email, strrpos($email, '@') + 1);
    }

	/**
	 * Can't store ASCII or Unicode characters below U+0020 in XML file so we put a token in the XML
	 * (except for HTAB, CR & LF)
	 * The tokens we have chosen are the Unicode Characters 'SYMBOL FOR xxx' (U+2400 onwards)
	 * Here we prepare the symbols mapping to the actual characters.
	 */
	private function prepareDecodeParameters() {
		$needles = array(' ', mb_convert_encoding('&#9229;&#9226;', 'UTF-8', 'HTML-ENTITIES'));
		$substitutes = array(' ', chr(13).chr(10));
		for ($i = 0; $i < 32; $i++) {
			// PHP bug doesn't allow us to use hex notation (http://bugs.php.net/48645)
			$entity = mb_convert_encoding('&#'.(string) (9216 + $i).';', 'UTF-8', 'HTML-ENTITIES');
			$needles[] = $entity;
			$substitutes[] = chr($i);
		}
		$this->searchChars = $needles;
		$this->replaceChars	= $substitutes;
	}

	private function getSearch() {
		if ($this->searchChars === NULL) {
			$this->prepareDecodeParameters();
		}
		return $this->searchChars;
	}

	private function getReplace() {
		if ($this->replaceChars === NULL) {
			$this->prepareDecodeParameters();
		}
		return $this->replaceChars;
	}
}