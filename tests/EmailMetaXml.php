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
		return $this->getMetaStatus($this->getMetaConstantName($diagnosis));
	}

	/**
	 * Return analysis ID by its numeric value
	 * 
	 * @param   mixed  $diagnosis
	 * @return  string
	 */
	private function getMetaConstantName($diagnosis) {
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
