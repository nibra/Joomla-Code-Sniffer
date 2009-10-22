<?php
/**
 * Joomla Coding Standard. (Derived from PEAR standard with minor changes.)
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2009 Open Source Matters, Inc.
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD License
 * @version   SVN: $Id: JoomlaCodingStandard.php 269131 2008-11-17 05:04:07Z paladin $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Joomla Coding Standard. (Derived from PEAR standard with minor changes.)
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2009 Open Source Matters, Inc.
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD License
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_Joomla_JoomlaCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{


    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The Joomla standard uses some generic sniffs.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
                'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
                'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
                'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',
               );

    }//end getIncludedSniffs()


}//end class
?>
