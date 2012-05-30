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

	private $metaPath = '../is_email/test/meta.xml';
	private $xmlPath = '../is_email/test/tests.xml';
	private $xmlCache = array();

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
	 * @return  \SimpleXMLIterator
	 */
	public function getTestCases() {
		return $this->getXml($this->xmlPath)->test;
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
}
