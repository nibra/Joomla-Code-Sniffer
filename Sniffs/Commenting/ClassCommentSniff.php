<?php
/**
 * Parses and verifies the doc comments for classes. (Changed slightly from PEAR sniffer)
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ClassCommentSniff.php 270281 2008-12-02 02:38:34Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (!class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true)) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (!class_exists('Joomla_Sniffs_Commenting_FileCommentSniff', true)) {
    $error = 'Class Joomla_Sniffs_Commenting_FileCommentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Paladin <arlen.walker@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 *
 * @property  PHP_CodeSniffer_CommentParser_ClassCommentParser|PHP_CodeSniffer_CommentParser_AbstractParser $commentParser
 * @property  PHP_CodeSniffer_File $currentFile
 * @method    getIndentation
 */
class Joomla_Sniffs_Commenting_ClassCommentSniff extends Joomla_Sniffs_Commenting_FileCommentSniff
{
    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
        'category' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'precedes @package',
        ),
        'package' => array(
            'required' => true,
            'allow_multiple' => false,
            'order_text' => 'follows @category',
        ),
        'subpackage' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @package',
        ),
        'author' => array(
            'required' => false,
            'allow_multiple' => true,
            'order_text' => 'follows @subpackage (if used) or @package',
        ),
        'copyright' => array(
            'required' => false,
            'allow_multiple' => true,
            'order_text' => 'follows @author',
        ),
        'license' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @copyright (if used) or @author',
        ),
        'version' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @license',
        ),
        'link' => array(
            'required' => false,
            'allow_multiple' => true,
            'order_text' => 'follows @version',
        ),
        'see' => array(
            'required' => false,
            'allow_multiple' => true,
            'order_text' => 'follows @link',
        ),
        'since' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @see (if used) or @link',
        ),
        'deprecated' => array(
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @since (if used) or @see (if used) or @link',
        ),
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_CLASS,
            T_INTERFACE,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $type = strtolower($tokens[$stackPtr]['content']);
        $errorData = array($type);
        $find = array(
            T_ABSTRACT,
            T_WHITESPACE,
            T_FINAL,
        );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a %s comment';
            $phpcsFile->addError($error, $stackPtr, 'WrongStyle', $errorData);
            return;
        } else {
            if ($commentEnd === false
                || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT
            ) {
                $phpcsFile->addError('Missing %s doc comment', $stackPtr, 'Missing', $errorData);
                return;
            }
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(
                            T_WHITESPACE,
                            ($newlineToken + 1),
                            $stackPtr,
                            false,
                            $phpcsFile->eolChar
                        );

                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $error = 'Missing %s doc comment';
                            $phpcsFile->addError($error, ($stackPtr + 1), 'Missing', $errorData);
                            return;
                        }
                    }
                }
            }
        }

        $comment = $phpcsFile->getTokensAsString(
            $commentStart,
            ($commentEnd - $commentStart + 1)
        );

        // Parse the class comment.docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line, 'FailedParse');
            return;
        }

        /** @var PHP_CodeSniffer_CommentParser_CommentElement $comment */
        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Doc comment is empty for %s';
            $phpcsFile->addError($error, $commentStart, 'Empty', $errorData);
            return;
        }

        // No extra newline before short description.
        $short = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before $type comment short description";
            $phpcsFile->addError($error, ($commentStart + 1), 'SpacingBeforeShort', $errorData);
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in %s comments';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1), 'SpacingAfterShort', $errorData);
            }

            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in %s comments';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($error, ($commentStart + $newlineCount), 'SpacingBeforeTags', $errorData);
                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);
    }

    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart Position in the stack where the comment started.
     * @param int $commentEnd   Position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        $docBlock = (get_class($this) === 'Joomla_Sniffs_Commenting_FileCommentSniff')
            ? 'file' : 'class';
        $foundTags = $this->commentParser->getTagOrders();
        $orderIndex = 0;
        $indentation = array();
        $longestTag = 0;
        $errorPos = 0;

        foreach ($this->tags as $tag => $info) {

            // Required tag missing.
            if ($info['required'] === true && in_array($tag, $foundTags) === false) {
                $error = "Missing @$tag tag in $docBlock comment";
                $this->currentFile->addError($error, $commentEnd);
                continue;
            }

            // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($info['allow_multiple'] === true) {
                $tagName .= 's';
            }

            $getMethod = 'get' . $tagName;
            $tagElement = $this->commentParser->$getMethod();
            if (is_null($tagElement) === true || empty($tagElement) === true) {
                continue;
            }

            $errorPos = $commentStart;
            if (is_array($tagElement) === false) {
                /** @var PHP_CodeSniffer_CommentParser_AbstractDocElement $tagElement */
                $errorPos = ($commentStart + $tagElement->getLine());
            }

            // Get the tag order.
            $foundIndexes = array_keys($foundTags, $tag);

            if (count($foundIndexes) > 1) {
                // Multiple occurance not allowed.
                if ($info['allow_multiple'] === false) {
                    $error = "Only 1 @$tag tag is allowed in a $docBlock comment";
                    $this->currentFile->addError($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $i = 0;
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index !== $count) {
                            $errorPosIndex
                                = ($errorPos + $tagElement[$i]->getLine());
                            $error = "@$tag tags must be grouped together";
                            $this->currentFile->addError($error, $errorPosIndex);
                        }

                        $i++;
                        $count++;
                    }
                }
            }
            //end if

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                if (is_array($tagElement) === true && empty($tagElement) === false) {
                    $errorPos += $tagElement[0]->getLine();
                }

                $orderText = $info['order_text'];
                $error = "The @$tag tag is in the wrong order; the tag $orderText";
                $this->currentFile->addError($error, $errorPos);
            }

            // Store the indentation for checking.
            $len = strlen($tag);
            if ($len > $longestTag) {
                $longestTag = $len;
            }

            if (is_array($tagElement) === true) {
                /** @var PHP_CodeSniffer_CommentParser_AbstractDocElement $element */
                foreach ($tagElement as $key => $element) {
                    $indentation[] = array(
                        'tag' => $tag,
                        'space' => $this->getIndentation($tag, $element),
                        'line' => $element->getLine(),
                    );
                }
            } else {
                $indentation[] = array(
                    'tag' => $tag,
                    'space' => $this->getIndentation($tag, $tagElement),
                );
            }

            $method = 'process' . $tagName;
            if (method_exists($this, $method) === true) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                if (is_array($tagElement) === true) {
                    /** @var PHP_CodeSniffer_CommentParser_SingleElement $element */
                    foreach ($tagElement as $key => $element) {
                        $element->process(
                            $this->currentFile,
                            $commentStart,
                            $docBlock
                        );
                    }
                } else {
                    /** @var PHP_CodeSniffer_CommentParser_SingleElement $tagElement */
                    $tagElement->process(
                        $this->currentFile,
                        $commentStart,
                        $docBlock
                    );
                }
            }
        }

        foreach ($indentation as $indentInfo) {
            if ($indentInfo['space'] !== 0
                && $indentInfo['space'] > ($longestTag + 4)
            ) {
                $expected = (($longestTag - strlen($indentInfo['tag'])) + 1);
                $space = ($indentInfo['space'] - strlen($indentInfo['tag']));
                $error = "@$indentInfo[tag] tag comment indented incorrectly. ";
                $error .= "Expected $expected spaces but found $space.";

                $getTagMethod = 'get' . ucfirst($indentInfo['tag']);

                if ($this->tags[$indentInfo['tag']]['allow_multiple'] === true) {
                    $line = $indentInfo['line'];
                } else {
                    /** @var PHP_CodeSniffer_CommentParser_AbstractDocElement $tagElem */
                    $tagElem = $this->commentParser->$getTagMethod();
                    $line = $tagElem->getLine();
                }

                $this->currentFile->addError($error, ($commentStart + $line));
            }
        }
    }

    /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            $matches = array();
            if (empty($content) === true) {
                $error = 'Content missing for @version tag in doc comment';
                $this->currentFile->addError($error, $errorPos, 'EmptyVersion');
            } else {
                if ((strstr($content, 'Release:') === false)) {
                    $error = 'Invalid version "%s" in doc comment; consider "Release: <package_version>" instead';
                    $data = array($content);
                    $this->currentFile->addWarning($error, $errorPos, 'InvalidVersion', $data);
                }
            }
        }
    }

}

?>