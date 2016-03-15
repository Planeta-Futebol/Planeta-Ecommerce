<?php
/**
 * ShopInDev
 *
 * @category    ShopInDev
 * @package     ShopInDev_SuperPageSpeed
 * @copyright   Copyright (c) 2014 ShopInDev
 * @license     http://opensource.org/licenses/GPL-3.0 GNU General Public License (GPL)
 */

class ShopInDev_SuperPageSpeed_Model_Minify {

	/**
	 * Placeholders for HTML
	 * @var array
	 */
	public $htmlPlaceholders = array();

	/**
	 * Compress HTML to reduce load
	 * @param string $html
	 * @return string
	 **/
	public function htmlMinify($html){

		// Replace PREs with placeholders
		$html = preg_replace_callback(
			'/\\s*(<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i',
			array($this, 'setPlaceholder'),
			$html
		);

		// Replace TEXTAREAs with placeholders
		$html = preg_replace_callback(
			'/\\s*(<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i',
			array($this, 'setPlaceholder'),
			$html
		);

		// Replace SCRIPTs with placeholders
		$html = preg_replace_callback(
			'/\\s*(<script\\b[^>]*?>[\\s\\S]*?<\\/script>)\\s*/i',
			array($this, 'setScriptPlaceholder'),
			$html
		);

		// Remove HTML comments
		$html = preg_replace_callback(
			'/(<!--[\\s\\S]*?-->)/',
			array($this, 'removeComments'),
			$html
		);

		// Trim each line
		$html = str_replace(array("\n","\r","\t"), '', $html);
		$html = preg_replace('/^\\s+|\\s+$/m', '', $html);

		// Remove white space around block/undisplayed elements
		$html = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
				. '|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
				. '|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
				. '|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
				. '|ul)\\b[^>]*>)/i', '$1', $html);

		// Remove white space outside of all elements
		$html = preg_replace(
			'/>(\\s(?:\\s*))?([^<]+)(\\s(?:\s*))?</',
			'>$1$2$3<',
			$html
		);

		// Remove all multiple whitespace
		$html = preg_replace('/\s+/', ' ', $html);

		// Replace placeholders
		$this->htmlPlaceholders = array_reverse($this->htmlPlaceholders, TRUE);

		foreach( $this->htmlPlaceholders as $key => $value ){
			$html = str_replace($key, $value, $html);
		}

		return $html;
	}

	/**
	 * Generate a placeholderID and replace the string with placeholderID
	 * @param array $match
	 * @return string
	 */
	protected function setPlaceholder($match){

		$placeholder = 'MINIFY_PLACEHOLDER'. count($this->htmlPlaceholders);
		$this->htmlPlaceholders[ $placeholder ] = $match[1];

		return $placeholder;
	}

	/**
	 * Generate a script placeholderID and replace the string with placeholderID
	 * This method also remove tabs in scripts
	 * @param array $match
	 * @return string
	 */
	protected function setScriptPlaceholder($match){

		$match[1] = str_replace("\t", '', $match[1]);
		$match[1] = preg_replace('/^\\s+/m', '', $match[1]);

		return $this->setPlaceholder($match);
	}

	/**
	 * Remove HTML comments
	 * Do not remote IE conditional comments
	 * @param array $match
	 * @return string
	 */
	protected function removeComments($match){
		return ( FALSE !== strpos($match[1], '<!--[')
				 OR FALSE !== strpos($match[1], '<![') ) ? $match[0] : '';
	}

}