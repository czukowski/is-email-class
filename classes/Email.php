<?php
/**
 * Email class
 *
 * @package  Email
 * @author   Dominic Sayers <dominic@sayers.cc>
 * @author   Korney Czukowski
 * @license  BSD License
 */
namespace Actum\Utils;

class Email {
	// The following part of the code is generated using data from tests/meta.xml.
	// Beware of making manual alterations!
	// Categories
	const ISEMAIL_VALID_CATEGORY = 1;
	const ISEMAIL_DNSWARN = 7;
	const ISEMAIL_RFC5321 = 15;
	const ISEMAIL_CFWS = 31;
	const ISEMAIL_DEPREC = 63;
	const ISEMAIL_RFC5322 = 127;
	const ISEMAIL_ERR = 255;

	// Diagnoses
	// Address is valid
	const ISEMAIL_VALID = 0;
	// Address is valid but a DNS check was not successful
	const ISEMAIL_DNSWARN_NO_MX_RECORD = 5;
	const ISEMAIL_DNSWARN_NO_RECORD = 6;
	// Address is valid for SMTP but has unusual elements
	const ISEMAIL_RFC5321_TLD = 9;
	const ISEMAIL_RFC5321_TLDNUMERIC = 10;
	const ISEMAIL_RFC5321_QUOTEDSTRING = 11;
	const ISEMAIL_RFC5321_ADDRESSLITERAL = 12;
	const ISEMAIL_RFC5321_IPV6DEPRECATED = 13;
	// Address is valid within the message but cannot be used unmodified for the envelope
	const ISEMAIL_CFWS_COMMENT = 17;
	const ISEMAIL_CFWS_FWS = 18;
	// Address contains deprecated elements but may still be valid in restricted contexts
	const ISEMAIL_DEPREC_LOCALPART = 33;
	const ISEMAIL_DEPREC_FWS = 34;
	const ISEMAIL_DEPREC_QTEXT = 35;
	const ISEMAIL_DEPREC_QP = 36;
	const ISEMAIL_DEPREC_COMMENT = 37;
	const ISEMAIL_DEPREC_CTEXT = 38;
	const ISEMAIL_DEPREC_CFWS_NEAR_AT = 49;
	// The address is only valid according to the broad definition of RFC 5322. It is otherwise invalid.
	const ISEMAIL_RFC5322_DOMAIN = 65;
	const ISEMAIL_RFC5322_TOOLONG = 66;
	const ISEMAIL_RFC5322_LOCAL_TOOLONG = 67;
	const ISEMAIL_RFC5322_DOMAIN_TOOLONG = 68;
	const ISEMAIL_RFC5322_LABEL_TOOLONG = 69;
	const ISEMAIL_RFC5322_DOMAINLITERAL = 70;
	const ISEMAIL_RFC5322_DOMLIT_OBSDTEXT = 71;
	const ISEMAIL_RFC5322_IPV6_GRPCOUNT = 72;
	const ISEMAIL_RFC5322_IPV6_2X2XCOLON = 73;
	const ISEMAIL_RFC5322_IPV6_BADCHAR = 74;
	const ISEMAIL_RFC5322_IPV6_MAXGRPS = 75;
	const ISEMAIL_RFC5322_IPV6_COLONSTRT = 76;
	const ISEMAIL_RFC5322_IPV6_COLONEND = 77;
	// Address is invalid for any purpose
	const ISEMAIL_ERR_EXPECTING_DTEXT = 129;
	const ISEMAIL_ERR_NOLOCALPART = 130;
	const ISEMAIL_ERR_NODOMAIN = 131;
	const ISEMAIL_ERR_CONSECUTIVEDOTS = 132;
	const ISEMAIL_ERR_ATEXT_AFTER_CFWS = 133;
	const ISEMAIL_ERR_ATEXT_AFTER_QS = 134;
	const ISEMAIL_ERR_ATEXT_AFTER_DOMLIT = 135;
	const ISEMAIL_ERR_EXPECTING_QPAIR = 136;
	const ISEMAIL_ERR_EXPECTING_ATEXT = 137;
	const ISEMAIL_ERR_EXPECTING_QTEXT = 138;
	const ISEMAIL_ERR_EXPECTING_CTEXT = 139;
	const ISEMAIL_ERR_BACKSLASHEND = 140;
	const ISEMAIL_ERR_DOT_START = 141;
	const ISEMAIL_ERR_DOT_END = 142;
	const ISEMAIL_ERR_DOMAINHYPHENSTART = 143;
	const ISEMAIL_ERR_DOMAINHYPHENEND = 144;
	const ISEMAIL_ERR_UNCLOSEDQUOTEDSTR = 145;
	const ISEMAIL_ERR_UNCLOSEDCOMMENT = 146;
	const ISEMAIL_ERR_UNCLOSEDDOMLIT = 147;
	const ISEMAIL_ERR_FWS_CRLF_X2 = 148;
	const ISEMAIL_ERR_FWS_CRLF_END = 149;
	const ISEMAIL_ERR_CR_NO_LF = 150;
	// End of generated code

	// Function control
	const ISEMAIL_THRESHOLD =  16;

	// Email parts
	const ISEMAIL_COMPONENT_LOCALPART =  0;
	const ISEMAIL_COMPONENT_DOMAIN =  1;
	const ISEMAIL_COMPONENT_LITERAL =  2;
	const ISEMAIL_CONTEXT_COMMENT =  3;
	const ISEMAIL_CONTEXT_FWS =  4;
	const ISEMAIL_CONTEXT_QUOTEDSTRING =  5;
	const ISEMAIL_CONTEXT_QUOTEDPAIR =  6;

	// Miscellaneous string constants
	const ISEMAIL_STRING_AT =  '@';
	const ISEMAIL_STRING_BACKSLASH =  '\\';
	const ISEMAIL_STRING_DOT =  '.';
	const ISEMAIL_STRING_DQUOTE =  '"';
	const ISEMAIL_STRING_OPENPARENTHESIS =  '(';
	const ISEMAIL_STRING_CLOSEPARENTHESIS = ')';
	const ISEMAIL_STRING_OPENSQBRACKET =  '[';
	const ISEMAIL_STRING_CLOSESQBRACKET =  ']';
	const ISEMAIL_STRING_HYPHEN =  '-';
	const ISEMAIL_STRING_COLON =  ':';
	const ISEMAIL_STRING_DOUBLECOLON =  '::';
	const ISEMAIL_STRING_SP =  ' ';
	const ISEMAIL_STRING_HTAB =  "\t";
	const ISEMAIL_STRING_CR =  "\r";
	const ISEMAIL_STRING_LF =  "\n";
	const ISEMAIL_STRING_IPV6TAG =  'IPv6:';
	// US-ASCII visible characters not valid for atext (http://tools.ietf.org/html/rfc5322#section-3.2.3)
	const ISEMAIL_STRING_SPECIALS =  '()<>[]:;@\\,."';

	/**
	 * @var  string
	 */
	private $email;
	/**
	 * @var  array
	 */
	private $return_status;
	/**
	 * @var  integer
	 */
	private $final_status;
	/**
	 * @var  integer  Parse the address into components, character by character
	 */
	private $raw_length;
	/**
	 * @var  integer
	 */
	private $crlf_count;
	/**
	 * @var  integer  Where we are
	 */
	private $context;
	/**
	 * @var  integer  Where we have been
	 */
	private $context_stack;
	/**
	 * @var  integer  Where we just came from
	 */
	private $context_prior;
	/**
	 * @var  char  The current character
	 */
	private $token;
	/**
	 * @var  integer  Current token index
	 */
	private $pointer;
	/**
	 * @var  char  The previous character
	 */
	private $token_prior;
	/**
	 * @var  array  For the components of the address
	 */
	private $parsedata;
	/**
	 * @var  array  For the dot-atom elements of the address
	 */
	private $atomlist;
	/**
	 * @var  integer
	 */
	private $element_count;
	/**
	 * @var  integer
	 */
	private $element_len;
	/**
	 * @var  boolean  Hyphen cannot occur at the end of a subdomain
	 */
	private $hyphen_flag;
	/**
	 * @var  boolean  CFWS can only appear at the end of the element
	 */
	private $end_or_die;

	public function __construct($email) {
		$this->email = $email;
	}

	/**
	 * @param  boolean  $checkDNS 
	 */
	public function parse($checkDNS) {
		$this->return_status = array(self::ISEMAIL_VALID);

		$this->raw_length = strlen($this->email);
		$this->context = self::ISEMAIL_COMPONENT_LOCALPART;
		$this->context_stack = array($this->context);
		$this->context_prior = self::ISEMAIL_COMPONENT_LOCALPART;
		$this->token = '';
		$this->token_prior = '';
		$this->parsedata = array(
			self::ISEMAIL_COMPONENT_LOCALPART => '',
			self::ISEMAIL_COMPONENT_DOMAIN => '',
		);
		$this->atomlist = array(
			self::ISEMAIL_COMPONENT_LOCALPART => array(''),
			self::ISEMAIL_COMPONENT_DOMAIN => array(''),
		);
		$this->element_count = 0;
		$this->element_len = 0;
		$this->hyphen_flag = FALSE;
		$this->end_or_die = FALSE;

	//-echo "<table style=\"clear:left;\">"; // debug
		for ($this->pointer = 0; $this->pointer < $this->raw_length; $this->pointer++) {
			$this->token = $this->email[$this->pointer];
	//-echo "<tr><td><strong>$context|",(($end_or_die) ? 'true' : 'false'),"|$token|" . max($return_status) . "</strong></td>"; // debug

			switch ($this->context) {
				case self::ISEMAIL_COMPONENT_LOCALPART:
					// Local-part
					$this->parse_local_part();
					break;
				case self::ISEMAIL_COMPONENT_DOMAIN:
					// Domain
					$this->parse_component_domain();
					break;
				case self::ISEMAIL_COMPONENT_LITERAL:
					// Domain literal
					$this->parse_component_literal();
					break;
				case self::ISEMAIL_CONTEXT_QUOTEDSTRING:
					// Quoted string
					$this->parse_context_quotedstring();
					break;
				case self::ISEMAIL_CONTEXT_QUOTEDPAIR:
					// Quoted pair
					$this->parse_context_quotedpair();
					break;
				case self::ISEMAIL_CONTEXT_COMMENT:
					// Comment
					$this->parse_context_comment();
					break;
				case self::ISEMAIL_CONTEXT_FWS:
					// Folding White Space
					$this->parse_context_fws();
					break;
				default:
					// A context we aren't expecting
					throw new \UndefinedValueException("Unknown context: $this->context");
			}

	//-echo "<td>$context|",(($end_or_die) ? 'true' : 'false'),"|$token|" . max($return_status) . "</td></tr>"; // debug
			if ( (int) max($this->return_status) > self::ISEMAIL_RFC5322) {
				// No point going on if we've got a fatal error
				break;
			}
		}

		// Some simple final tests
		if ( (int) max($this->return_status) < self::ISEMAIL_RFC5322) {
			if ($this->context === self::ISEMAIL_CONTEXT_QUOTEDSTRING) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_UNCLOSEDQUOTEDSTR;
			}
			elseif ($this->context === self::ISEMAIL_CONTEXT_QUOTEDPAIR) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_BACKSLASHEND;
			}
			elseif ($this->context === self::ISEMAIL_CONTEXT_COMMENT) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_UNCLOSEDCOMMENT;
			}
			elseif ($this->context === self::ISEMAIL_COMPONENT_LITERAL) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_UNCLOSEDDOMLIT;
			}
			elseif ($this->token === self::ISEMAIL_STRING_CR) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_FWS_CRLF_END;
			}
			elseif ($this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] === '') {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_NODOMAIN;
			}
			elseif ($this->element_len === 0) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_DOT_END;
			}
			elseif ($this->hyphen_flag) {
				$this->return_status[] = self::ISEMAIL_ERR_DOMAINHYPHENEND;
			}
			elseif (strlen($this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN]) > 255) {
				// http://tools.ietf.org/html/rfc5321#section-4.5.3.1.2
				//   The maximum total length of a domain name or number is 255 octets.
				$this->return_status[] = self::ISEMAIL_RFC5322_DOMAIN_TOOLONG;
			}
			elseif (strlen($this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART].self::ISEMAIL_STRING_AT.$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN]) > 254) {
				// http://tools.ietf.org/html/rfc5321#section-4.1.2
				//   Forward-path   = Path
				// 
				//   Path           = "<" [ A-d-l ":" ] Mailbox ">"
				//
				// http://tools.ietf.org/html/rfc5321#section-4.5.3.1.3
				//   The maximum total length of a reverse-path or forward-path is 256
				//   octets (including the punctuation and element separators).
				// 
				// Thus, even without (obsolete) routing information, the Mailbox can
				// only be 254 characters long. This is confirmed by this verified
				// erratum to RFC 3696:
				// 
				// http://www.rfc-editor.org/errata_search.php?rfc=3696&eid=1690
				//   However, there is a restriction in RFC 2821 on the length of an
				//   address in MAIL and RCPT commands of 254 characters.  Since addresses
				//   that do not fit in those fields are not normally useful, the upper
				//   limit on address lengths should normally be considered to be 254.
				$this->return_status[] = self::ISEMAIL_RFC5322_TOOLONG;
			}
			elseif ($this->element_len > 63) {
				// http://tools.ietf.org/html/rfc1035#section-2.3.4
				//   labels          63 octets or less
				$this->return_status[] = self::ISEMAIL_RFC5322_LABEL_TOOLONG;
			}
		}

		// Check DNS?
		$dns_checked = FALSE;

		if ($checkDNS && ( (int) max($this->return_status) < self::ISEMAIL_DNSWARN) && function_exists('dns_get_record')) {
			// http://tools.ietf.org/html/rfc5321#section-2.3.5
			//   Names that can
			//   be resolved to MX RRs or address (i.e., A or AAAA) RRs (as discussed
			//   in Section 5) are permitted, as are CNAME RRs whose targets can be
			//   resolved, in turn, to MX or address RRs.
			// 
			// http://tools.ietf.org/html/rfc5321#section-5.1
			//   The lookup first attempts to locate an MX record associated with the
			//   name.  If a CNAME record is found, the resulting name is processed as
			//   if it were the initial name. ... If an empty list of MXs is returned,
			//   the address is treated as if it was associated with an implicit MX
			//   RR, with a preference of 0, pointing to that host.
			// 
			// is_email() author's note: We will regard the existence of a CNAME to be
			// sufficient evidence of the domain's existence. For performance reasons
			// we will not repeat the DNS lookup for the CNAME's target, but we will
			// raise a warning because we didn't immediately find an MX record.
			if ($this->element_count === 0) {
				// Checking TLD DNS seems to work only if you explicitly check from the root
				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= '.';
			}

			// Not using checkdnsrr because of a suspected bug in PHP 5.3 (http://bugs.php.net/bug.php?id=51844)
			$result = @dns_get_record($this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN], DNS_MX);

			if ((is_bool($result) && ! (bool) $result)) {
				// Domain can't be found in DNS
				$this->return_status[] = self::ISEMAIL_DNSWARN_NO_RECORD;
			}
			else {
				if (count($result) === 0) {
					// MX-record for domain can't be found
					$this->return_status[] = self::ISEMAIL_DNSWARN_NO_MX_RECORD;
					$result = @dns_get_record($this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN], DNS_A + DNS_CNAME);

					if (count($result) === 0) {
						// No usable records for the domain can be found
						$this->return_status[] = self::ISEMAIL_DNSWARN_NO_RECORD;
					}
				}
				else {
					$dns_checked = TRUE;
				}
			}
		}

		// Check for TLD addresses
		// -----------------------
		// TLD addresses are specifically allowed in RFC 5321 but they are
		// unusual to say the least. We will allocate a separate
		// status to these addresses on the basis that they are more likely
		// to be typos than genuine addresses (unless we've already
		// established that the domain does have an MX record)
		// 
		// http://tools.ietf.org/html/rfc5321#section-2.3.5
		//   In the case
		//   of a top-level domain used by itself in an email address, a single
		//   string is used without any dots.  This makes the requirement,
		//   described in more detail below, that only fully-qualified domain
		//   names appear in SMTP transactions on the public Internet,
		//   particularly important where top-level domains are involved.
		// 
		// TLD format
		// ----------
		// The format of TLDs has changed a number of times. The standards
		// used by IANA have been largely ignored by ICANN, leading to
		// confusion over the standards being followed. These are not defined
		// anywhere, except as a general component of a DNS host name (a label).
		// However, this could potentially lead to 123.123.123.123 being a
		// valid DNS name (rather than an IP address) and thereby creating
		// an ambiguity. The most authoritative statement on TLD formats that
		// the author can find is in a (rejected!) erratum to RFC 1123
		// submitted by John Klensin, the author of RFC 5321:
		// 
		// http://www.rfc-editor.org/errata_search.php?rfc=1123&eid=1353
		//   However, a valid host name can never have the dotted-decimal
		//   form #.#.#.#, since this change does not permit the highest-level
		//   component label to start with a digit even if it is not all-numeric.
		if ( ! $dns_checked && ( (int) max($this->return_status) < self::ISEMAIL_DNSWARN)) {
			if ($this->element_count === 0) {
				$this->return_status[] = self::ISEMAIL_RFC5321_TLD;
			}
			if (is_numeric($this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count][0])) {
				$this->return_status[] = self::ISEMAIL_RFC5321_TLDNUMERIC;
			}
		}

		$this->return_status = array_unique($this->return_status);
		$this->final_status = (int) max($this->return_status);

		// remove redundant self::ISEMAIL_VALID
		if (count($this->return_status) !== 1) {
			array_shift($this->return_status);
		}

		$this->parsedata['status'] = $this->return_status;
	}

	private function parse_local_part() {
		// http://tools.ietf.org/html/rfc5322#section-3.4.1
		//   local-part      =   dot-atom / quoted-string / obs-local-part
		//
		//   dot-atom        =   [CFWS] dot-atom-text [CFWS]
		//
		//   dot-atom-text   =   1*atext *("." 1*atext)
		//
		//   quoted-string   =   [CFWS]
		//                       DQUOTE *([FWS] qcontent) [FWS] DQUOTE
		//                       [CFWS]
		//
		//   obs-local-part  =   word *("." word)
		//
		//   word            =   atom / quoted-string
		//
		//   atom            =   [CFWS] 1*atext [CFWS]
		switch ($this->token) {
			case self::ISEMAIL_STRING_OPENPARENTHESIS:
				// Comment
				if ($this->element_len === 0) {
					// Comments are OK at the beginning of an element
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_CFWS_COMMENT : self::ISEMAIL_DEPREC_COMMENT;
				}
				else {
					// We can't start a comment in the middle of an element, so this better be the end
					$this->return_status[] = self::ISEMAIL_CFWS_COMMENT;
					$this->end_or_die = TRUE;
				}

				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_COMMENT;
				break;
			case self::ISEMAIL_STRING_DOT:
				// Next dot-atom element
				if ($this->element_len === 0) {
					// Another dot, already? Fatal error
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_ERR_DOT_START : self::ISEMAIL_ERR_CONSECUTIVEDOTS;
				}
				elseif ($this->end_or_die) {
					// The entire local-part can be a quoted string for RFC 5321
					// If it's just one atom that is quoted then it's an RFC 5322 obsolete form
					$this->return_status[] = self::ISEMAIL_DEPREC_LOCALPART;
				}

				// CFWS & quoted strings are OK again now we're at the beginning of an element (although they are obsolete forms)
				$this->end_or_die = FALSE;
				$this->element_len = 0;
				$this->element_count++;
				$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] = '';

				break;
			case self::ISEMAIL_STRING_DQUOTE:
				// Quoted string
				if ($this->element_len === 0) {
					// The entire local-part can be a quoted string for RFC 5321
					// If it's just one atom that is quoted then it's an RFC 5322 obsolete form
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_RFC5321_QUOTEDSTRING : self::ISEMAIL_DEPREC_LOCALPART;

					$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
					$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= $this->token;
					$this->element_len++;
					// Quoted string must be the entire element
					$this->end_or_die = TRUE;
					$this->context_stack[] = $this->context;
					$this->context = self::ISEMAIL_CONTEXT_QUOTEDSTRING;
				}
				else {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_ATEXT;
				}

				break;
			case self::ISEMAIL_STRING_CR:
			case self::ISEMAIL_STRING_SP:
			case self::ISEMAIL_STRING_HTAB:
				// Folding White Space
				if (($this->token === self::ISEMAIL_STRING_CR) && ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
					break;
				}

				if ($this->element_len === 0) {
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_CFWS_FWS : self::ISEMAIL_DEPREC_FWS;
				}
				else {
					// We can't start FWS in the middle of an element, so this better be the end
					$this->end_or_die = TRUE;
				}

				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_FWS;
				$this->token_prior = $this->token;

				break;
			case self::ISEMAIL_STRING_AT:
				// @
				// At this point we should have a valid local-part
				if (count($this->context_stack) !== 1) {
					throw new \UndefinedValueException('Unexpected item on context stack');
				}

				if ($this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] === '') {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_NOLOCALPART;
				}
				elseif ($this->element_len === 0) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_DOT_END;
				}
				elseif (strlen($this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART]) > 64) {
					// http://tools.ietf.org/html/rfc5321#section-4.5.3.1.1
					//   The maximum total length of a user name or other local-part is 64
					//   octets.
					$this->return_status[] = self::ISEMAIL_RFC5322_LOCAL_TOOLONG;
				}
				elseif (($this->context_prior === self::ISEMAIL_CONTEXT_COMMENT) || ($this->context_prior === self::ISEMAIL_CONTEXT_FWS)) {
					// http://tools.ietf.org/html/rfc5322#section-3.4.1
					//   Comments and folding white space
					//   SHOULD NOT be used around the "@" in the addr-spec.
					//
					// http://tools.ietf.org/html/rfc2119
					// 4. SHOULD NOT   This phrase, or the phrase "NOT RECOMMENDED" mean that
					//    there may exist valid reasons in particular circumstances when the
					//    particular behavior is acceptable or even useful, but the full
					//    implications should be understood and the case carefully weighed
					//    before implementing any behavior described with this label.
					$this->return_status[] = self::ISEMAIL_DEPREC_CFWS_NEAR_AT;
				}

				// Clear everything down for the domain parsing
				$this->context = self::ISEMAIL_COMPONENT_DOMAIN;
				$this->context_stack = array($this->context);
				$this->element_count = 0;
				$this->element_len = 0;
				// CFWS can only appear at the end of the element
				$this->end_or_die = FALSE;

				break;
			default:
				// atext
				// http://tools.ietf.org/html/rfc5322#section-3.2.3
				//    atext           =   ALPHA / DIGIT /    ; Printable US-ASCII
				//                        "!" / "#" /        ;  characters not including
				//                        "$" / "%" /        ;  specials.  Used for atoms.
				//                        "&" / "'" /
				//                        "*" / "+" /
				//                        "-" / "/" /
				//                        "=" / "?" /
				//                        "^" / "_" /
				//                        "`" / "{" /
				//                        "|" / "}" /
				//                        "~"
				if ($this->end_or_die) {
					// We have encountered atext where it is no longer valid
					switch ($this->context_prior) {
						case self::ISEMAIL_CONTEXT_COMMENT:
						case self::ISEMAIL_CONTEXT_FWS:
							$this->return_status[] = self::ISEMAIL_ERR_ATEXT_AFTER_CFWS;
							break;
						case self::ISEMAIL_CONTEXT_QUOTEDSTRING:
							$this->return_status[] = self::ISEMAIL_ERR_ATEXT_AFTER_QS;
							break;
						default:
							throw new \UndefinedValueException("More atext found where none is allowed, but unrecognised prior context: $this->context_prior");
					}
				}
				else {
					$this->context_prior = $this->context;
					$ord = ord($this->token);

					if (($ord < 33) || ($ord > 126) || ($ord === 10) || ( ! is_bool(strpos(self::ISEMAIL_STRING_SPECIALS, $this->token)))) {
						// Fatal error
						$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_ATEXT;
					}

					$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
					$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= $this->token;
					$this->element_len++;
				}
		}
	}

	public function parse_component_domain() {
		// http://tools.ietf.org/html/rfc5322#section-3.4.1
		//   domain          =   dot-atom / domain-literal / obs-domain
		//
		//   dot-atom        =   [CFWS] dot-atom-text [CFWS]
		//
		//   dot-atom-text   =   1*atext *("." 1*atext)
		//
		//   domain-literal  =   [CFWS] "[" *([FWS] dtext) [FWS] "]" [CFWS]
		//
		//   dtext           =   %d33-90 /          ; Printable US-ASCII
		//                       %d94-126 /         ;  characters not including
		//                       obs-dtext          ;  "[", "]", or "\"
		//
		//   obs-domain      =   atom *("." atom)
		//
		//   atom            =   [CFWS] 1*atext [CFWS]
		// 
		// http://tools.ietf.org/html/rfc5321#section-4.1.2
		//   Mailbox        = Local-part "@" ( Domain / address-literal )
		//
		//   Domain         = sub-domain *("." sub-domain)
		//
		//   address-literal  = "[" ( IPv4-address-literal /
		//                    IPv6-address-literal /
		//                    General-address-literal ) "]"
		//                    ; See Section 4.1.3
		// 
		// http://tools.ietf.org/html/rfc5322#section-3.4.1
		//      Note: A liberal syntax for the domain portion of addr-spec is
		//      given here.  However, the domain portion contains addressing
		//      information specified by and used in other protocols (e.g.,
		//      [RFC1034], [RFC1035], [RFC1123], [RFC5321]).  It is therefore
		//      incumbent upon implementations to conform to the syntax of
		//      addresses for the context in which they are used.
		// is_email() author's note: it's not clear how to interpret this in
		// the context of a general email address validator. The conclusion I
		// have reached is this: "addressing information" must comply with
		// RFC 5321 (and in turn RFC 1035), anything that is "semantically
		// invisible" must comply only with RFC 5322.
		switch ($this->token) {
			case self::ISEMAIL_STRING_OPENPARENTHESIS:
				// Comment
				if ($this->element_len === 0) {
					// Comments at the start of the domain are deprecated in the text
					// Comments at the start of a subdomain are obs-domain
					// (http://tools.ietf.org/html/rfc5322#section-3.4.1)
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_DEPREC_CFWS_NEAR_AT : self::ISEMAIL_DEPREC_COMMENT;
				}
				else {
					// We can't start a comment in the middle of an element, so this better be the end
					$this->return_status[] = self::ISEMAIL_CFWS_COMMENT;
					$this->end_or_die = TRUE;
				}

				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_COMMENT;
				break;
			case self::ISEMAIL_STRING_DOT:
				// Next dot-atom element
				if ($this->element_len === 0) {
					// Another dot, already? Fatal error
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_ERR_DOT_START : self::ISEMAIL_ERR_CONSECUTIVEDOTS;
				}
				elseif ($this->hyphen_flag) {
					// Previous subdomain ended in a hyphen. Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_DOMAINHYPHENEND;
				}
				elseif ($this->element_len > 63) {
					// Nowhere in RFC 5321 does it say explicitly that the
					// domain part of a Mailbox must be a valid domain according
					// to the DNS standards set out in RFC 1035, but this *is*
					// implied in several places. For instance, wherever the idea
					// of host routing is discussed the RFC says that the domain
					// must be looked up in the DNS. This would be nonsense unless
					// the domain was designed to be a valid DNS domain. Hence we
					// must conclude that the RFC 1035 restriction on label length
					// also applies to RFC 5321 domains.
					// 
					// http://tools.ietf.org/html/rfc1035#section-2.3.4
					//   labels          63 octets or less
					$this->return_status[] = self::ISEMAIL_RFC5322_LABEL_TOOLONG;
				}

				// CFWS is OK again now we're at the beginning of an element (although it may be obsolete CFWS)
				$this->end_or_die = FALSE;
				$this->element_len = 0;
				$this->element_count++;
				$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] = '';
				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
				break;
			case self::ISEMAIL_STRING_OPENSQBRACKET:
				// Domain literal
				if ($this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] === '') {
					// Domain literal must be the only component
					$this->end_or_die = TRUE;
					$this->element_len++;
					$this->context_stack[] = $this->context;
					$this->context = self::ISEMAIL_COMPONENT_LITERAL;
					$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
					$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] .= $this->token;
					$this->parsedata[self::ISEMAIL_COMPONENT_LITERAL] = '';
				}
				else {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_ATEXT;
				}

				break;
			case self::ISEMAIL_STRING_CR:
			case self::ISEMAIL_STRING_SP:
			case self::ISEMAIL_STRING_HTAB:
				// Folding White Space
				if (($this->token === self::ISEMAIL_STRING_CR) && ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
					break;
				}

				if ($this->element_len === 0) {
					$this->return_status[] = ($this->element_count === 0) ? self::ISEMAIL_DEPREC_CFWS_NEAR_AT : self::ISEMAIL_DEPREC_FWS;
				}
				else {
					// We can't start FWS in the middle of an element, so this better be the end
					$this->return_status[] = self::ISEMAIL_CFWS_FWS;
					$this->end_or_die = TRUE;
				}

				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_FWS;
				$this->token_prior = $this->token;
				break;
			default:
				// atext
				// RFC 5322 allows any atext...
				// http://tools.ietf.org/html/rfc5322#section-3.2.3
				//    atext           =   ALPHA / DIGIT /    ; Printable US-ASCII
				//                        "!" / "#" /        ;  characters not including
				//                        "$" / "%" /        ;  specials.  Used for atoms.
				//                        "&" / "'" /
				//                        "*" / "+" /
				//                        "-" / "/" /
				//                        "=" / "?" /
				//                        "^" / "_" /
				//                        "`" / "{" /
				//                        "|" / "}" /
				//                        "~"
				// 
				// But RFC 5321 only allows letter-digit-hyphen to comply with DNS rules (RFCs 1034 & 1123)
				// http://tools.ietf.org/html/rfc5321#section-4.1.2
				//   sub-domain     = Let-dig [Ldh-str]
				// 
				//   Let-dig        = ALPHA / DIGIT
				// 
				//   Ldh-str        = *( ALPHA / DIGIT / "-" ) Let-dig
				//
				if ($this->end_or_die) {
					// We have encountered atext where it is no longer valid
					switch ($this->context_prior) {
						case self::ISEMAIL_CONTEXT_COMMENT:
						case self::ISEMAIL_CONTEXT_FWS:
							$this->return_status[] = self::ISEMAIL_ERR_ATEXT_AFTER_CFWS;
							break;
						case self::ISEMAIL_COMPONENT_LITERAL:
							$this->return_status[] = self::ISEMAIL_ERR_ATEXT_AFTER_DOMLIT;
							break;
						default:
							throw new \UndefinedValueException("More atext found where none is allowed, but unrecognised prior context: $this->context_prior");
					}
				}

				$ord = ord($this->token);
				// Assume this token isn't a hyphen unless we discover it is
				$this->hyphen_flag = FALSE;

				if (($ord < 33) || ($ord > 126) || ( ! is_bool(strpos(self::ISEMAIL_STRING_SPECIALS, $this->token)))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_ATEXT;
				}
				elseif ($this->token === self::ISEMAIL_STRING_HYPHEN) {
					if ($this->element_len === 0) {
						// Hyphens can't be at the beginning of a subdomain. Fatal error
						$this->return_status[] = self::ISEMAIL_ERR_DOMAINHYPHENSTART;
					}

					$this->hyphen_flag = TRUE;
				}
				elseif ( ! (($ord > 47 && $ord < 58) || ($ord > 64 && $ord < 91) || ($ord > 96 && $ord < 123))) {
					// Not an RFC 5321 subdomain, but still OK by RFC 5322
					$this->return_status[] = self::ISEMAIL_RFC5322_DOMAIN;
				}

				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] .= $this->token;
				$this->element_len++;
		}
	}

	private function parse_component_literal() {
		// http://tools.ietf.org/html/rfc5322#section-3.4.1
		//   domain-literal  =   [CFWS] "[" *([FWS] dtext) [FWS] "]" [CFWS]
		//
		//   dtext           =   %d33-90 /          ; Printable US-ASCII
		//                       %d94-126 /         ;  characters not including
		//                       obs-dtext          ;  "[", "]", or "\"
		//
		//   obs-dtext       =   obs-NO-WS-CTL / quoted-pair
		switch ($this->token) {
			// End of domain literal
			case self::ISEMAIL_STRING_CLOSESQBRACKET:
				if ( (int) max($this->return_status) < self::ISEMAIL_DEPREC) {
					// Could be a valid RFC 5321 address literal, so let's check
					// 
					// http://tools.ietf.org/html/rfc5321#section-4.1.2
					//   address-literal  = "[" ( IPv4-address-literal /
					//                    IPv6-address-literal /
					//                    General-address-literal ) "]"
					//                    ; See Section 4.1.3
					//
					// http://tools.ietf.org/html/rfc5321#section-4.1.3
					//   IPv4-address-literal  = Snum 3("."  Snum)
					//
					//   IPv6-address-literal  = "IPv6:" IPv6-addr
					//
					//   General-address-literal  = Standardized-tag ":" 1*dcontent
					//
					//   Standardized-tag  = Ldh-str
					//                     ; Standardized-tag MUST be specified in a
					//                     ; Standards-Track RFC and registered with IANA
					//
					//   dcontent       = %d33-90 / ; Printable US-ASCII
					//                  %d94-126 ; excl. "[", "\", "]"
					//
					//   Snum           = 1*3DIGIT
					//                  ; representing a decimal integer
					//                  ; value in the range 0 through 255
					//
					//   IPv6-addr      = IPv6-full / IPv6-comp / IPv6v4-full / IPv6v4-comp
					//
					//   IPv6-hex       = 1*4HEXDIG
					//
					//   IPv6-full      = IPv6-hex 7(":" IPv6-hex)
					//
					//   IPv6-comp      = [IPv6-hex *5(":" IPv6-hex)] "::"
					//                  [IPv6-hex *5(":" IPv6-hex)]
					//                  ; The "::" represents at least 2 16-bit groups of
					//                  ; zeros.  No more than 6 groups in addition to the
					//                  ; "::" may be present.
					//
					//   IPv6v4-full    = IPv6-hex 5(":" IPv6-hex) ":" IPv4-address-literal
					//
					//   IPv6v4-comp    = [IPv6-hex *3(":" IPv6-hex)] "::"
					//                  [IPv6-hex *3(":" IPv6-hex) ":"]
					//                  IPv4-address-literal
					//                  ; The "::" represents at least 2 16-bit groups of
					//                  ; zeros.  No more than 4 groups in addition to the
					//                  ; "::" and IPv4-address-literal may be present.
					//
					// is_email() author's note: We can't use ip2long() to validate
					// IPv4 addresses because it accepts abbreviated addresses
					// (xxx.xxx.xxx), expanding the last group to complete the address.
					// filter_var() validates IPv6 address inconsistently (up to PHP 5.3.3
					// at least) -- see http://bugs.php.net/bug.php?id=53236 for example
					$max_groups = 8;
					$matchesIP = array();
					$index = FALSE;
					$addressliteral = $this->parsedata[self::ISEMAIL_COMPONENT_LITERAL];

					// Extract IPv4 part from the end of the address-literal (if there is one)
					if (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressliteral, $matchesIP) > 0) {
						$index = strrpos($addressliteral, $matchesIP[0]);
						if ($index !== 0) {
							// Convert IPv4 part to IPv6 format for further testing
							$addressliteral = substr($addressliteral, 0, $index).'0:0';
						}
					}

					if ($index === 0) {
						// Nothing there except a valid IPv4 address, so...
						$this->return_status[] = self::ISEMAIL_RFC5321_ADDRESSLITERAL;
					}
					elseif (strncasecmp($addressliteral, self::ISEMAIL_STRING_IPV6TAG, 5) !== 0) {
						$this->return_status[] = self::ISEMAIL_RFC5322_DOMAINLITERAL;
					}
					else {
						// Revision 2.7: Daniel Marschall's new IPv6 testing strategy
						$IPv6 = substr($addressliteral, 5);
						$matchesIP = explode(self::ISEMAIL_STRING_COLON, $IPv6);
						$groupCount = count($matchesIP);
						$index = strpos($IPv6,self::ISEMAIL_STRING_DOUBLECOLON);

						if ($index === FALSE) {
							// We need exactly the right number of groups
							if ($groupCount !== $max_groups) {
								$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_GRPCOUNT;
							}
						}
						else {
							if ($index !== strrpos($IPv6, self::ISEMAIL_STRING_DOUBLECOLON)) {
								$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_2X2XCOLON;
							}
							else {
								if ($index === 0 || $index === (strlen($IPv6) - 2)) {
									// RFC 4291 allows :: at the start or end of an address with 7 other groups in addition
									$max_groups++;
								}

								if ($groupCount > $max_groups) {
									$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_MAXGRPS;
								}
								elseif ($groupCount === $max_groups) {
									// Eliding a single "::"
									$this->return_status[] = self::ISEMAIL_RFC5321_IPV6DEPRECATED;
								}
							}
						}

						// Revision 2.7: Daniel Marschall's new IPv6 testing strategy
						if ((substr($IPv6, 0, 1) === self::ISEMAIL_STRING_COLON) && (substr($IPv6, 1,  1) !== self::ISEMAIL_STRING_COLON)) {
							// Address starts with a single colon
							$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_COLONSTRT;
						}
						elseif ((substr($IPv6, -1) === self::ISEMAIL_STRING_COLON) && (substr($IPv6, -2, 1) !== self::ISEMAIL_STRING_COLON)) {
							// Address ends with a single colon
							$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_COLONEND;
						}
						elseif (count(preg_grep('/^[0-9A-Fa-f]{0,4}$/', $matchesIP, PREG_GREP_INVERT)) !== 0) {
							// Check for unmatched characters
							$this->return_status[] = self::ISEMAIL_RFC5322_IPV6_BADCHAR;
						}
						else {
							$this->return_status[] = self::ISEMAIL_RFC5321_ADDRESSLITERAL;
						}
					}
				}
				else {
					$this->return_status[] = self::ISEMAIL_RFC5322_DOMAINLITERAL;
				}

				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] .= $this->token;
				$this->element_len++;
				$this->context_prior = $this->context;
				$this->context = (int) array_pop($this->context_stack);
				break;
			case self::ISEMAIL_STRING_BACKSLASH:
				$this->return_status[] = self::ISEMAIL_RFC5322_DOMLIT_OBSDTEXT;
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_QUOTEDPAIR;
				break;
			case self::ISEMAIL_STRING_CR:
			case self::ISEMAIL_STRING_SP:
			case self::ISEMAIL_STRING_HTAB:
				// Folding White Space
				if (($this->token === self::ISEMAIL_STRING_CR) && ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
					break;
				}

				$this->return_status[] = self::ISEMAIL_CFWS_FWS;
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_FWS;
				$this->token_prior = $this->token;
				break;
			default:
				// dtext
				// http://tools.ietf.org/html/rfc5322#section-3.4.1
				//   dtext           =   %d33-90 /          ; Printable US-ASCII
				//                       %d94-126 /         ;  characters not including
				//                       obs-dtext          ;  "[", "]", or "\"
				//
				//   obs-dtext       =   obs-NO-WS-CTL / quoted-pair
				//
				//   obs-NO-WS-CTL   =   %d1-8 /            ; US-ASCII control
				//                       %d11 /             ;  characters that do not
				//                       %d12 /             ;  include the carriage
				//                       %d14-31 /          ;  return, line feed, and
				//                       %d127              ;  white space characters
				$ord = ord($this->token);

				// CR, LF, SP & HTAB have already been parsed above
				if (($ord > 127) || ($ord === 0) || ($this->token === self::ISEMAIL_STRING_OPENSQBRACKET)) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_DTEXT;
					break;
				}
				elseif (($ord < 33) || ($ord === 127)) {
					$this->return_status[] = self::ISEMAIL_RFC5322_DOMLIT_OBSDTEXT;
				}

				$this->parsedata[self::ISEMAIL_COMPONENT_LITERAL] .= $this->token;
				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] .= $this->token;
				$this->element_len++;
		}
	}

	private function parse_context_quotedstring() {
		// http://tools.ietf.org/html/rfc5322#section-3.2.4
		//   quoted-string   =   [CFWS]
		//                       DQUOTE *([FWS] qcontent) [FWS] DQUOTE
		//                       [CFWS]
		// 
		//   qcontent        =   qtext / quoted-pair
		switch ($this->token) {
			case self::ISEMAIL_STRING_BACKSLASH:
				// Quoted pair
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_QUOTEDPAIR;
				break;
			case self::ISEMAIL_STRING_CR:
			case self::ISEMAIL_STRING_HTAB:
				// Folding White Space
				// Inside a quoted string, spaces are allowed as regular characters.
				// It's only FWS if we include HTAB or CRLF
				if (($this->token === self::ISEMAIL_STRING_CR) && ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
					break;
				}

				// http://tools.ietf.org/html/rfc5322#section-3.2.2
				//   Runs of FWS, comment, or CFWS that occur between lexical tokens in a
				//   structured header field are semantically interpreted as a single
				//   space character.
				// 
				// http://tools.ietf.org/html/rfc5322#section-3.2.4
				//   the CRLF in any FWS/CFWS that appears within the quoted-string [is]
				//   semantically "invisible" and therefore not part of the quoted-string
				$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= self::ISEMAIL_STRING_SP;
				$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= self::ISEMAIL_STRING_SP;
				$this->element_len++;

				$this->return_status[] = self::ISEMAIL_CFWS_FWS;
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_FWS;
				$this->token_prior = $this->token;
				break;
			case self::ISEMAIL_STRING_DQUOTE:
				// End of quoted string
				$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= $this->token;
				$this->element_len++;
				$this->context_prior = $this->context;
				$this->context = (int) array_pop($this->context_stack);
				break;
			default:
				// qtext
				// http://tools.ietf.org/html/rfc5322#section-3.2.4
				//   qtext           =   %d33 /             ; Printable US-ASCII
				//                       %d35-91 /          ;  characters not including
				//                       %d93-126 /         ;  "\" or the quote character
				//                       obs-qtext
				//
				//   obs-qtext       =   obs-NO-WS-CTL
				//
				//   obs-NO-WS-CTL   =   %d1-8 /            ; US-ASCII control
				//                       %d11 /             ;  characters that do not
				//                       %d12 /             ;  include the carriage
				//                       %d14-31 /          ;  return, line feed, and
				//                       %d127              ;  white space characters
				$ord = ord($this->token);

				if (($ord > 127) || ($ord === 0) || ($ord === 10)) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_QTEXT;
				}
				elseif (($ord < 32) || ($ord === 127)) {
					$this->return_status[] = self::ISEMAIL_DEPREC_QTEXT;
				}

				$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= $this->token;
				$this->element_len++;
		}

		// http://tools.ietf.org/html/rfc5322#section-3.4.1
		//   If the
		//   string can be represented as a dot-atom (that is, it contains no
		//   characters other than atext characters or "." surrounded by atext
		//   characters), then the dot-atom form SHOULD be used and the quoted-
		//   string form SHOULD NOT be used.

		// TODO
	}

	private function parse_context_quotedpair() {
		// http://tools.ietf.org/html/rfc5322#section-3.2.1
		//   quoted-pair     =   ("\" (VCHAR / WSP)) / obs-qp
		//
		//   VCHAR           =  %d33-126            ; visible (printing) characters
		//   WSP             =  SP / HTAB           ; white space
		//
		//   obs-qp          =   "\" (%d0 / obs-NO-WS-CTL / LF / CR)
		//
		//   obs-NO-WS-CTL   =   %d1-8 /            ; US-ASCII control
		//                       %d11 /             ;  characters that do not
		//                       %d12 /             ;  include the carriage
		//                       %d14-31 /          ;  return, line feed, and
		//                       %d127              ;  white space characters
		//
		// i.e. obs-qp       =  "\" (%d0-8, %d10-31 / %d127)
		$ord = ord($this->token);

		if ($ord > 127) {
			// Fatal error
			$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_QPAIR;
		}
		elseif ((($ord < 31) && ($ord !== 9)) || ($ord === 127)) {
			// SP & HTAB are allowed
			$this->return_status[] = self::ISEMAIL_DEPREC_QP;
		}

		// At this point we know where this qpair occurred so
		// we could check to see if the character actually
		// needed to be quoted at all.
		// http://tools.ietf.org/html/rfc5321#section-4.1.2
		//   the sending system SHOULD transmit the
		//   form that uses the minimum quoting possible.
		// TODO: check whether the character needs to be quoted (escaped) in this context
		$this->context_prior = $this->context;
		// End of qpair
		$this->context = (int) array_pop($this->context_stack);
		$this->token = self::ISEMAIL_STRING_BACKSLASH.$this->token;

		switch ($this->context) {
			case self::ISEMAIL_CONTEXT_COMMENT:
				break;
			case self::ISEMAIL_CONTEXT_QUOTEDSTRING:
				$this->parsedata[self::ISEMAIL_COMPONENT_LOCALPART] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_LOCALPART][$this->element_count] .= $this->token;
				// The maximum sizes specified by RFC 5321 are octet counts, so we must include the backslash
				$this->element_len += 2;
				break;
			case self::ISEMAIL_COMPONENT_LITERAL:
				$this->parsedata[self::ISEMAIL_COMPONENT_DOMAIN] .= $this->token;
				$this->atomlist[self::ISEMAIL_COMPONENT_DOMAIN][$this->element_count] .= $this->token;
				// The maximum sizes specified by RFC 5321 are octet counts, so we must include the backslash
				$this->element_len += 2;
				break;
			default:
				throw new \UndefinedValueException("Quoted pair logic invoked in an invalid context: $this->context");
		}
	}

	private function parse_context_comment() {
		// http://tools.ietf.org/html/rfc5322#section-3.2.2
		//   comment         =   "(" *([FWS] ccontent) [FWS] ")"
		//
		//   ccontent        =   ctext / quoted-pair / comment
		switch ($this->token) {
			case self::ISEMAIL_STRING_OPENPARENTHESIS:
				// Nested comment
				// Nested comments are OK
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_COMMENT;
				break;
			case self::ISEMAIL_STRING_CLOSEPARENTHESIS:
				// End of comment
				$this->context_prior = $this->context;
				$this->context = (int) array_pop($this->context_stack);

				// http://tools.ietf.org/html/rfc5322#section-3.2.2
				//   Runs of FWS, comment, or CFWS that occur between lexical tokens in a
				//   structured header field are semantically interpreted as a single
				//   space character.
				//
				// is_email() author's note: This *cannot* mean that we must add a
				// space to the address wherever CFWS appears. This would result in
				// any addr-spec that had CFWS outside a quoted string being invalid
				// for RFC 5321.
//				if (($context === self::ISEMAIL_COMPONENT_LOCALPART) || ($context === self::ISEMAIL_COMPONENT_DOMAIN)) {
//					$parsedata[$context] .= self::ISEMAIL_STRING_SP;
//					$atomlist[$context][$element_count] .= self::ISEMAIL_STRING_SP;
//					$element_len++;
//				}

				break;
			case self::ISEMAIL_STRING_BACKSLASH:
				// Quoted pair
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_QUOTEDPAIR;
				break;
			case self::ISEMAIL_STRING_CR:
			case self::ISEMAIL_STRING_SP:
			case self::ISEMAIL_STRING_HTAB:
				// Folding White Space
				if (($this->token === self::ISEMAIL_STRING_CR) && ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF))) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
					break;
				}

				$this->return_status[] = self::ISEMAIL_CFWS_FWS;
				$this->context_stack[] = $this->context;
				$this->context = self::ISEMAIL_CONTEXT_FWS;
				$this->token_prior = $this->token;
				break;
			default:
				// ctext
				// http://tools.ietf.org/html/rfc5322#section-3.2.3
				//   ctext           =   %d33-39 /          ; Printable US-ASCII
				//                       %d42-91 /          ;  characters not including
				//                       %d93-126 /         ;  "(", ")", or "\"
				//                       obs-ctext
				//
				//   obs-ctext       =   obs-NO-WS-CTL
				//
				//   obs-NO-WS-CTL   =   %d1-8 /            ; US-ASCII control
				//                       %d11 /             ;  characters that do not
				//                       %d12 /             ;  include the carriage
				//                       %d14-31 /          ;  return, line feed, and
				//                       %d127              ;  white space characters
				$ord = ord($this->token);

				if (($ord > 127) || ($ord === 0) || ($ord === 10)) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_EXPECTING_CTEXT;
					break;
				}
				elseif (($ord < 32) || ($ord === 127)) {
					$this->return_status[] = self::ISEMAIL_DEPREC_CTEXT;
				}
		}
	}

	private function parse_context_fws() {
		// http://tools.ietf.org/html/rfc5322#section-3.2.2
		//   FWS             =   ([*WSP CRLF] 1*WSP) /  obs-FWS
		//                                          ; Folding white space
		// 
		// But note the erratum:
		// http://www.rfc-editor.org/errata_search.php?rfc=5322&eid=1908:
		//   In the obsolete syntax, any amount of folding white space MAY be
		//   inserted where the obs-FWS rule is allowed.  This creates the
		//   possibility of having two consecutive "folds" in a line, and
		//   therefore the possibility that a line which makes up a folded header
		//   field could be composed entirely of white space.
		//
		//   obs-FWS         =   1*([CRLF] WSP)
		if ($this->token_prior === self::ISEMAIL_STRING_CR) {
			if ($this->token === self::ISEMAIL_STRING_CR) {
				// Fatal error
				$this->return_status[] = self::ISEMAIL_ERR_FWS_CRLF_X2;
				return;
			}

			if (isset($this->crlf_count)) {
				if (++$this->crlf_count > 1) {
					// Multiple folds = obsolete FWS
					$this->return_status[] = self::ISEMAIL_DEPREC_FWS;
				}
			}
			else {
				$this->crlf_count = 1;
			}
		}

		switch ($this->token) {
			case self::ISEMAIL_STRING_CR:
				if ((++$this->pointer === $this->raw_length) || ($this->email[$this->pointer] !== self::ISEMAIL_STRING_LF)) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_CR_NO_LF;
				}
				break;
			case self::ISEMAIL_STRING_SP:
			case self::ISEMAIL_STRING_HTAB:
				break;
			default:
				if ($this->token_prior === self::ISEMAIL_STRING_CR) {
					// Fatal error
					$this->return_status[] = self::ISEMAIL_ERR_FWS_CRLF_END;
					break;
				}

				if (isset($this->crlf_count)) {
					unset($this->crlf_count);
				}

				// End of FWS
				$this->context_prior = $this->context;
				$this->context = (int) array_pop($this->context_stack);

				// http://tools.ietf.org/html/rfc5322#section-3.2.2
				//   Runs of FWS, comment, or CFWS that occur between lexical tokens in a
				//   structured header field are semantically interpreted as a single
				//   space character.
				//
				// is_email() author's note: This *cannot* mean that we must add a
				// space to the address wherever CFWS appears. This would result in
				// any addr-spec that had CFWS outside a quoted string being invalid
				// for RFC 5321.
//				if (($context === self::ISEMAIL_COMPONENT_LOCALPART) || ($context === self::ISEMAIL_COMPONENT_DOMAIN)) {
//					$parsedata[$context] .= self::ISEMAIL_STRING_SP;
//					$atomlist[$context][$element_count] .= self::ISEMAIL_STRING_SP;
//					$element_len++;
//				}

				// Look at this token again in the parent context
				$this->pointer--;
		}

		$this->token_prior = $this->token;
	}

	public function status() {
		return $this->final_status;
	}

	/**
	 * Check that an email address conforms to RFCs 5321, 5322 and others
	 *
	 * As of Version 3.0, we are now distinguishing clearly between a Mailbox
	 * as defined by RFC 5321 and an addr-spec as defined by RFC 5322. Depending
	 * on the context, either can be regarded as a valid email address. The
	 * RFC 5321 Mailbox specification is more restrictive (comments, white space
	 * and obsolete forms are not allowed)
	 *
	 * @param string   $email       The email address to check
	 * @param boolean  $checkDNS    If true then a DNS check for MX records will be made
	 * @param mixed    $errorlevel  Determines the boundary between valid and invalid addresses.
	 *                              Status codes above this number will be returned as-is,
	 *                              status codes below will be returned as self::ISEMAIL_VALID. Thus the
	 *                              calling program can simply look for self::ISEMAIL_VALID if it is
	 *                              only interested in whether an address is valid or not. The
	 *                              errorlevel will determine how "picky" is_email() is about
	 *                              the address.
	 *
	 *                              If omitted or passed as false then is_email() will return
	 *                              true or false rather than an integer error or warning.
	 *
	 *                              NB Note the difference between $errorlevel = false and
	 *                              $errorlevel = 0
	 * @param array    $parsedata   If passed, returns the parsed address components
	 */
	public static function is_email($email, $checkDNS = FALSE, $errorlevel = FALSE) {
		// Check that $email is a valid address. Read the following RFCs to understand the constraints:
		//  (http://tools.ietf.org/html/rfc5321)
		//  (http://tools.ietf.org/html/rfc5322)
		//  (http://tools.ietf.org/html/rfc4291#section-2.2)
		//  (http://tools.ietf.org/html/rfc1123#section-2.1)
		//  (http://tools.ietf.org/html/rfc3696) (guidance only)
		// version 2.0: Enhance $diagnose parameter to $errorlevel
		// version 3.0: Introduced status categories
		// revision 3.1: BUG: $parsedata was passed by value instead of by reference

		if (is_bool($errorlevel)) {
			$threshold = self::ISEMAIL_VALID;
			$diagnose = (bool) $errorlevel;
		}
		else {
			$diagnose = TRUE;

			switch ( (int) $errorlevel) {
				case E_WARNING:
					// For backward compatibility
					$threshold = self::ISEMAIL_THRESHOLD;
					break;
				case E_ERROR:
					// For backward compatibility
					$threshold = self::ISEMAIL_VALID;
					break;
				default:
					$threshold = (int) $errorlevel;
			}
		}

		$instance = new self($email);
		$instance->parse($checkDNS);
		$final_status = $instance->status();

		if ($final_status < $threshold) {
			$final_status = self::ISEMAIL_VALID;
		}

		return ($diagnose) ? $final_status : ($final_status < self::ISEMAIL_THRESHOLD);
	}
}