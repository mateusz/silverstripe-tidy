<?php
/**
 * This cleaner attempts to use tidy through extension, or through commandline.
 */
class TidyHTMLCleaner extends HTMLCleaner {
	public function cleanHTML($content) {
		// Try to use the extension first
		if (extension_loaded('tidy')) {
			$tidy = tidy_parse_string($content,
				array(
					'clean' => true,
					'output-xhtml'	=> true,
					'show-body-only' => true,
					'wrap' => 0,
					'input-encoding' => 'utf8',
					'output-encoding' => 'utf8'
				)
			);

			$tidy->cleanRepair();
			return '' . $tidy;
		}

		// No PHP extension available, attempt to use CLI tidy.
		// This works both on Unix/Linux and Windows.
		$retval = null;
		$output = null;
		@exec('tidy --version', $output, $retval);
		if ($retval===0) {
			$input = escapeshellarg($content);
			$tidy = @`echo $input | tidy -q --show-body-only yes --input-encoding utf8 --output-encoding utf8 --wrap 0 --clean yes --output-xhtml yes`;
			return $tidy;
		}

		// Fall back to default
		$doc = new SS_HTMLValue($content);
		return $doc->getContent();
	}
}

