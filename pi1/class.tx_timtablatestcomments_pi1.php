<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Frank Nägler <mail@naegler.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'latest comments' for the 'timtab_latestcomments' extension.
 *
 * @author	Frank Nägler <mail@naegler.net>
 * @package	TYPO3
 * @subpackage	tx_timtablatestcomments
 */
class tx_timtablatestcomments_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_timtablatestcomments_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_timtablatestcomments_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'timtab_latestcomments';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->timtabconf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_timtab.'];
		 
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$this->init();

		$content = $this->getComments();
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	function init() {
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
	}
	
	function getComments() {
		$ret = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid, uid_tt_news, firstname, surname, entry',
					'tx_veguestbook_entries',
					'pid=' . $this->conf['commentsPid'] . $this->cObj->enableFields('tx_veguestbook_entries'),
					'',
					'uid DESC',
					$this->conf['listCount']
				);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// linkComments << link header tp POST by $row['uid_tt_news']
			$tmp = '';
			if ($this->conf['linkComments'] == 1) {
				$lnkText = $row['firstname'].' '.$row['surname'];

				$params = array(
					'tx_ttnews[tt_news]' => $row['uid_tt_news']
				);
                $conf = array(
                        'useCacheHash'     => 1,
                        'no_cache'         => 0,
                        'parameter'        => $this->timtabconf['blogPid'],
                        'additionalParams' => $this->conf['parent.']['addParams'].t3lib_div::implodeArrayForUrl('',$params,'',1).$this->pi_moreParams,
                );
				$link = $this->local_cObj->typoLink($lnkText, $conf);
				$tmp .= $this->local_cObj->stdWrap($link, $this->conf['authorWrap.']); 
			} else {
				$tmp .= $this->local_cObj->stdWrap($row['firstname'].' '.$row['surname'],$this->conf['authorWrap.']);
			}
			$tmp .= $this->local_cObj->stdWrap($this->trim($row['entry']), $this->conf['entryWrap.']);
			$tmp = $this->local_cObj->stdWrap($tmp, $this->conf['itemWrap.']);
			$ret .= $tmp;
		}
		return $this->local_cObj->stdWrap($ret, $this->conf['allWrap.']);
	}
	
	function trim($s) {
		$maxChars = $this->conf['comment.']['maxCharCount'];
		$ts = htmlspecialchars(stripslashes(substr($s, 0, $maxChars)));
		if (strlen($ts) < strlen($s))
			return $ts . '...';
		
		return $ts;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_latestcomments/pi1/class.tx_timtablatestcomments_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_latestcomments/pi1/class.tx_timtablatestcomments_pi1.php']);
}

?>