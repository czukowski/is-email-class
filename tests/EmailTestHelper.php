<?php
/**
 * Email test helper
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class EmailTestHelper {

	const ISEMAIL_STRING_UNKNOWN = '(unknown)';

	private $hasInternet;
	private $replaceChars;
	private $searchChars;
	private $metaPath;
	private $xmlCache = array();

	public function __construct($metaXmlPath) {
		$this->metaPath = $metaXmlPath;
	}

	/**
	 * Formats message for output
	 */
	public function getMessage($email, $expectedCode, $actualCode, $comment) {
		$actual = $this->getAnalysis($actualCode);
		$expected = $this->getAnalysis($expectedCode);
		$result = array(
			$email,
			str_repeat('-', mb_strlen($email, 'utf-8')),
			($expectedCode === NULL ? '' : 'Expected: '.$expected['description']),
			($actualCode === NULL ? '' : 'Actual: '.$actual['description']),
			$comment,
		);
		return implode("\n", array_filter($result));
	}

	/**
	 * Returns analysis by its ID (constant name) or numeric value
	 * 
	 * @return  array
	 */
	public function getAnalysis($actual) {
		return $this->getMetaStatus($this->getMetaConstantName($actual));
	}

	/**
	 * Return analysis ID by its numeric value
	 * 
	 * @return  string
	 */
	private function getMetaConstantName($actual) {
		if (is_int($actual)) {
			$nodes = $this->getXml($this->metaPath)
				->xpath("/meta/*/item/value[. = '$actual']/../@id");
			return (count($nodes) === 0) ? self::ISEMAIL_STRING_UNKNOWN : (string) $nodes[0]->id;
		}
		else if (is_string($actual)) {
			return $actual;
		}
		else {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
	}

	/**
	 * Getting Meta data from XML
	 * 
	 * @return  array
	 */
	private function getMetaStatus($constant) {
		try {
			$element = $this->getMetaElement("/meta/*/item[@id = '$constant']");
		}
		catch (\OutOfBoundsException $e) {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
		return array(
			'id' => $this->getElementProperty($element->attributes(), 'id'),
			'value' => (int) $this->getElementProperty($element, 'value'),
			'description' => $this->getElementProperty($element, 'description'),
			'category' => $this->getMetaCategory($this->getElementProperty($element, 'category')),
			'smtp' => $this->getMetaSmtp($this->getElementProperty($element, 'smtp')),
			'reference' => $this->getMetaReferences($element->reference),
		);
	}

	/**
	 * Getting Meta Category data from XML
	 * 
	 * @return  array 
	 */
	private function getMetaCategory($category) {
		try {
			$category = $this->getMetaElement("/meta/*/item[@id = '$category']");
		}
		catch (\OutOfBoundsException $e) {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
		return array(
			'id' => $this->getElementProperty($category->attributes(), 'id'),
			'description' => $this->getElementProperty($category, 'description'),
			'value' => (int) $this->getElementProperty($category, 'value'),
		);
	}

	/**
	 * Getting Meta SMTP data from XML
	 * 
	 * @return  array 
	 */
	private function getMetaSmtp($constant) {
		try {
			$smtp = $this->getMetaElement("/meta/*/item[@id = '$constant']");
		}
		catch (\OutOfBoundsException $e) {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
		return array(
			'id' => $this->getElementProperty($smtp->attributes(), 'id'),
			'description' => $this->getElementProperty($smtp, 'text'),
			'value' => $this->getElementProperty($smtp, 'value'),
		);
	}

	/**
	 * Getting Meta References data from XML
	 * 
	 * @return  array 
	 */
	private function getMetaReferences($references) {
		$result = array();
		foreach ($references as $reference) {
			$result[] = $this->getMetaReference($reference);
		}
		return $result;
	}

	/**
	 * Getting single Meta Reference data from XML
	 * 
	 * @return  array 
	 */
	private function getMetaReference($name) {
		try {
			$reference = $this->getMetaElement("/meta/*/item[@id = '$name']");
		}
		catch (\OutOfBoundsException $e) {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
		return array(
			'text' => $this->getElementProperty($reference, 'blockquote', NULL),
			'cite' => $this->getElementProperty($reference, 'cite', NULL),
			'link' => $this->getElementProperty($reference->attributes(), 'cite', NULL),
		);
	}

	/**
	 * Getting element data from XML using Xpath expression
	 * 
	 * @return  \SimpleXMLElement
	 */
	private function getMetaElement($query) {
		$nodes = $this->getXml($this->metaPath)
			->xpath($query);
		if ( ! count($nodes)) {
			throw new \OutOfBoundsException;
		}
		return $nodes[0];
	}

	/**
	 * Getting XML element property
	 * 
	 * @return  string
	 */
	private function getElementProperty($element, $propertyName, $default = self::ISEMAIL_STRING_UNKNOWN) {
		return $element->{$propertyName} ? (string) $element->{$propertyName} : $default;
	}

	/**
	 * Getting test cases from tests XML
	 * 
	 * @param   string  $relativePath
	 * @return  \SimpleXMLIterator
	 */
	public function getTestCases($relativePath) {
		$provide = array();
		foreach ($this->getXml($relativePath)->test as $testCase) {
			$provide[ (string) $testCase->attributes()->id] = $this->createTestCase($testCase);
		}
		return $provide;
	}

	/**
	 * @return  \SimpleXMLElement
	 */
	private function getXml($relativePath) {
		if ( ! isset($this->xmlCache[$relativePath])) {
			$this->xmlCache[$relativePath] = simplexml_load_file(realpath(__DIR__.'/'.$relativePath));
			if ( ! $this->xmlCache[$relativePath] instanceof \SimpleXMLElement) {
				throw new \Exception('XML load error');
			}
		}
		return $this->xmlCache[$relativePath];
	}

	/**
	 * Formats XML element to test case array
	 * 
	 * @param   \SimpleXMLElement  $testCase
	 * @return  array
	 */
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
	 * Retrieves diagnosis by its ID (constant name)
	 * 
	 * @param   mixed   $constantName
	 * @return  integer
	 */
	private function getExpectedDiagnosis($constantName) {
		$diagnosis = $this->getAnalysis($constantName);
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
}
