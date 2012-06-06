<?php
/**
 * EmailXml
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class EmailXml {

	/**
	 * @var  \SimpleXMLElement
	 */
	protected $xml;

	/**
	 * @param  string  $xmlPath 
	 */
	protected function loadXml($xmlPath) {
		$this->xml = simplexml_load_file($xmlPath);
		if ( ! $this->xml instanceof \SimpleXMLElement) {
			throw new \Exception('XML load error');
		}
	}
}