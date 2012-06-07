<?php

require_once __DIR__.'/bootstrap.php';

$args = $_SERVER['argv'];

call_user_func(function() use ($args) {

	$tokens = array(
		'start' => array(
			'// The following part of the code is generated using data from tests/meta.xml.',
			'// Beware of making manual alterations!',
		),
		'end' => '// End of generated code',
		'comment' => "\t// :comment",
		'constant' => "\tconst :name = :value;",
		'line' => "\t:line",
		'usage' => "\n\nUsage:\nphp update_from_meta.php Email.php\nphp update_from_meta.php Email.php > Email.php",
	);

	if (strpos($args[0], 'phpunit')) {
		die('This file isn\'t supposed to be run as a unit test!'.$tokens['usage']);
	}
	else if ( ! isset($args[1])) {
		die('Too few arguments'.$tokens['usage']);
	}
	$is_email_filename = realpath(__DIR__.'/'.$args[1]);
	if ( ! $is_email_filename) {
		die('File "'.$args[1].'" not found'.$tokens['usage']);
	}

	$meta_xml = new \Actum\Utils\EmailMetaXml(__DIR__.'/xml/meta.xml');
	$dummy_category = new \stdClass;
	$dummy_category->value = -1;
	$diagnose_categories = array($dummy_category);
	$output = array();

	foreach ($tokens['start'] as $line) {
		$output[] = strtr($tokens['line'], array(':line' => $line));
	}

	foreach (array('Categories', 'Diagnoses') as $category) {
		$output[] = strtr($tokens['comment'], array(':comment' => $category));
		foreach ($meta_xml->getRoot()->{$category}->item as $item) {
			if ($category === 'Categories') {
				$diagnose_categories[] = $item;
			}
			elseif ($category === 'Diagnoses') {
				if ($item->value > (int) reset($diagnose_categories)->value) {
					array_shift($diagnose_categories);
					$output[] = strtr($tokens['comment'], array(':comment' => reset($diagnose_categories)->description));
				}
			}
			$output[] = strtr($tokens['constant'], array(
				':name' => $item->attributes()->id,
				':value' => $item->value,
			));
		}
		$output[] = '';
	}

	$output[] = strtr($tokens['line'], array(':line' => $tokens['end']));

	function replace_regex($start, $end) {
		$tokens = array();
		foreach (array($start, $end) as $token) {
			$token = array_map('preg_quote', (array) $token);
			$tokens[] = implode('\x0d\x0a?\t*', $token);
		}
		return '#\t*'.implode('.*?', $tokens).'#s';
	}

	$is_email = file_get_contents($is_email_filename);
	echo preg_replace(replace_regex($tokens['start'], $tokens['end']), implode("\n", $output), $is_email);
});