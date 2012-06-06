<?php
/**
 * EmailMetaXml
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class EmailMetaXml extends EmailXml {

	const ISEMAIL_STRING_UNKNOWN = '(unknown)';

	public function __construct($xmlPath) {
		$this->loadXml($xmlPath);
	}

	/**
	 * Returns analysis by its ID (constant name) or numeric value
	 * 
	 * @param   mixed  $diagnosis
	 * @return  array
	 */
	public function getAnalysis($diagnosis) {
		return $this->getStatus($this->getConstantName($diagnosis));
	}

	/**
	 * Return analysis ID by its numeric value
	 * 
	 * @param   mixed  $diagnosis
	 * @return  string
	 */
	private function getConstantName($diagnosis) {
		if (is_int($diagnosis)) {
			$nodes = $this->xml->xpath("/meta/*/item/value[. = '$diagnosis']/../@id");
			return (count($nodes) === 0) ? self::ISEMAIL_STRING_UNKNOWN : (string) $nodes[0]->id;
		}
		else if (is_string($diagnosis)) {
			return $diagnosis;
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
	private function getStatus($constant) {
		try {
			$element = $this->getElement("/meta/*/item[@id = '$constant']");
		}
		catch (\OutOfBoundsException $e) {
			return self::ISEMAIL_STRING_UNKNOWN;
		}
		return array(
			'id' => $this->getElementProperty($element->attributes(), 'id'),
			'value' => (int) $this->getElementProperty($element, 'value'),
			'description' => $this->getElementProperty($element, 'description'),
			'category' => $this->getCategory($this->getElementProperty($element, 'category')),
			'smtp' => $this->getSmtp($this->getElementProperty($element, 'smtp')),
			'reference' => $this->getReferences($element->reference),
		);
	}

	/**
	 * Getting Meta Category data from XML
	 * 
	 * @return  array 
	 */
	private function getCategory($category) {
		try {
			$category = $this->getElement("/meta/*/item[@id = '$category']");
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
	private function getSmtp($constant) {
		try {
			$smtp = $this->getElement("/meta/*/item[@id = '$constant']");
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
	private function getReferences($references) {
		$result = array();
		foreach ($references as $reference) {
			$result[] = $this->getReference($reference);
		}
		return $result;
	}

	/**
	 * Getting single Meta Reference data from XML
	 * 
	 * @return  array 
	 */
	private function getReference($name) {
		try {
			$reference = $this->getElement("/meta/*/item[@id = '$name']");
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
	private function getElement($query) {
		$nodes = $this->xml->xpath($query);
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

}
