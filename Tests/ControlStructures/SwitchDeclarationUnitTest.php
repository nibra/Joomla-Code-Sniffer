<?php
/**
 * Unit test class for the SwitchDeclaration sniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @copyright 2010 Paladin Web Services. All rights reserved.
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Git: $Id: ControlSignatureUnitTest.php 226877 2007-01-10 03:14:44Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the SwitchDeclaration sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @copyright 2010 Paladin Web Services. All rights reserved.
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Joomla_Tests_ControlStructures_SwitchDeclarationUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                9   => 1,
                14  => 1,
                20  => 1,
                22  => 1,
                32  => 1,
                36  => 1,
                44  => 1,
                48  => 1,
                56  => 1,
                60  => 1,
                68  => 1,
                72  => 1,
                84  => 1,
                88  => 2,
                100 => 1,
                104 => 2,
                122 => 2,
                128 => 1,
                132 => 3,
                133 => 2,
                147 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class

?>
