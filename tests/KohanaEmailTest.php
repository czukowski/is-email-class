<?php
/**
 * KohanaEmailTest
 * 
 * @package  Email
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class KohanaEmailTest extends EmailTestCase {

	/**
	 * @dataProvider  provideEmails
	 */
	public function testIsEmail($email, $checkDns, $unused, $expected, $comment) {
		$actual = $this->isEmail($email, $checkDns);
		$this->assertEquals($expected, $actual, $comment);
	}

	/**
	 * Function based on Kohana 3.1 email validator
	 * Copyright (c) 2008-2011 Kohana Team
	 */
	public function isEmail($value, $validateDomain = FALSE) {
		$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
		$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
		$atom  = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
		$pair  = '\\x5c[\\x00-\\x7f]';

		$domain_literal = "\\x5b($dtext|$pair)*\\x5d";
		$quoted_string = "\\x22($qtext|$pair)*\\x22";
		$sub_domain = "($atom|$domain_literal)";
		$word = "($atom|$quoted_string)";
		$domain = "$sub_domain(\\x2e$sub_domain)*";
		$local_part = "$word(\\x2e$word)*";

		$expression = "/^$local_part\\x40$domain$/D";
		$validFormat = (bool) preg_match($expression, (string) $value);

		if ($validateDomain) {
			return $validFormat && $this->isValidEmailDomain($value);
		}
		return $validFormat;
	}
}