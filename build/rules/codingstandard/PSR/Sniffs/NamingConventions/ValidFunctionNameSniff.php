<?php
/**
 * PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * PSR1_Sniffs_NamingConventions_ValidFunctionNameSniff based on PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff.
 *
 * Ensures method names are correct depending on camelCaps, and that functions are named correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Miro Svrtan <miro@mirosvrtan.me>
 * @license   BSD Licence
 * @version   Release: 1.0.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR_Sniffs_NamingConventions_ValidFunctionNameSniff 
    extends PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff
{

    /**
     * Processes the tokens within the scope.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
     * @param int                  $stackPtr  The position where this token was
     *                                        found.
     * @param int                  $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        $className = $phpcsFile->getDeclarationName($currScope);
        $errorData = array($className.'::'.$methodName);

        // Is this a magic method. IE. is prefixed with "__".
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = substr($methodName, 2);
            if (in_array($magicPart, $this->magicMethods) === false) {
                 $error = 'Method name "%s" is invalid; 
                         only PHP magic methods should be prefixed with a double underscore';
                 
                 $phpcsFile->addError($error, $stackPtr, 'MethodDoubleUnderscore', $errorData);
            }

            return;
        }

        $methodProps    = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProps['scope'] === 'private') {
            $isPublic = false;
        } elseif ($methodProps['scope'] === 'protected') {
            $isPublic = false;
        } else {
            $isPublic = true;
        }
        
        
        $scope          = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

        // If it's public method, it must not have an underscore on the front.
        if ($isPublic === true && $methodName{0} === '_') {
            $error = '%s method name "%s" must not be prefixed with an underscore';
            $data  = array(
                      ucfirst($scope),
                      $errorData[0],
                     );
            $phpcsFile->addError($error, $stackPtr, 'PublicUnderscore', $data);
        }

        // If it's private/protected method, it should not have an underscore on the front.
        if ($isPublic === false && $methodName{0} === '_') {
            $warning = '%s method name "%s" should not be prefixed with an underscore';
            $data  = array(
                      ucfirst($scope),
                      $errorData[0],
                     );
            $phpcsFile->addWarning($warning, $stackPtr, 'PublicUnderscore', $data);
        }        

        $testMethodName = $methodName;
        // If method has underscore prefix remove it.
        if ($methodName{0} === '_') {
            $testMethodName = substr($methodName, 1);
        }

        if (PHP_CodeSniffer::isCamelCaps($testMethodName, false, true, false) === false) {
            if ($scopeSpecified === true) {
                $error = '%s method name "%s" is not in camel caps format';
                $data  = array(
                          ucfirst($scope),
                          $errorData[0],
                         );
                $phpcsFile->addError($error, $stackPtr, 'ScopeNotCamelCaps', $data);
            } else {
                $error = 'Method name "%s" is not in camel caps format';
                $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            }
        }
    }
}

