<?php
/**
 * EmailTestXml
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class EmailTestXml extends EmailXml{

	private $helper;
	private $replaceChars;
	private $searchChars;

	/**
	 * @param  EmailTestCase  $testHelper
	 * @param  string         $xmlPath 
	 */
	public function __construct(EmailTestCase $testHelper, $xmlPath) {
		$this->helper = $testHelper;
		$this->loadXml($xmlPath);
	}

	/**
	 * Getting test cases from tests XML
	 * 
	 * @return  \SimpleXMLIterator
	 */
	public function getTestCases() {
		$provide = array();
		foreach ($this->xml->test as $testCase) {
			$provide[ (string) $testCase->attributes()->id] = $this->createTestCase($testCase);
		}
		return $provide;
	}

	/**
	 * Formats XML element to test case array
	 * 
	 * @param   \SimpleXMLElement  $testCase
	 * @return  array
	 */
	private function createTestCase(\SimpleXMLElement $testCase) {
		$isValid = $this->getExpectedDiagnosis( (string) $testCase->diagnosis);
		if ( ! $this->helper->hasInternet() && $isValid <= Email::ISEMAIL_DNSWARN) {
			$isValid = Email::ISEMAIL_VALID;
		}
		$formallyValid = $isValid <= Email::ISEMAIL_RFC5321_IPV6DEPRECATED;
		return array(
			$this->getDecodedString($testCase->address),
			$this->helper->hasInternet(),
			$isValid,
			$formallyValid,
			$this->getDecodedString($testCase->comment),
		);
	}

	/**
	 * Retrieves diagnosis by its ID (constant name)
	 * 
	 * @param   mixed   $constantName
	 * @return  integer
	 */
	private function getExpectedDiagnosis($constantName) {
		$diagnosis = $this->helper->getAnalysis($constantName);
		return $diagnosis['value'];
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
	 * @return  array
	 */
	private function getSearch() {
		if ($this->searchChars === NULL) {
			$this->prepareDecodeParameters();
		}
		return $this->searchChars;
	}

	/**
	 * @return  array
	 */
	private function getReplace() {
		if ($this->replaceChars === NULL) {
			$this->prepareDecodeParameters();
		}
		return $this->replaceChars;
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
}