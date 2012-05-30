<?php
/**
 * NetteEmailTest
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class NetteEmailTest extends EmailTestCase {

	/**
	 * Function based on Nette 2 email validator
	 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
	 */
	public function isEmail($value, $validateDomain = FALSE) {
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)"; // quoted or unquoted
		$chars = "a-z0-9\x80-\xFF"; // superset of IDN
		$domain = "[$chars](?:[-$chars]{0,61}[$chars])"; // RFC 1034 one domain component
		$validFormat = (bool) preg_match("(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i", $value);
		if ($validateDomain) {
			return $validFormat && $this->isValidEmailDomain($value);
		}
		return $validFormat;
	}
}