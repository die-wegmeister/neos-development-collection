<?php
namespace TYPO3\TYPO3CR\Security\Authorization\Privilege\Node;

/*
 * This file is part of the TYPO3.TYPO3CR package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * A privilege to restrict reading of node properties.
 *
 * This is needed, as the technical implementation is not based on the entity privilege type, that
 * the read node privilege (retrieving the node at all) ist based on.
 */
class ReadNodePropertyPrivilege extends AbstractNodePropertyPrivilege
{
    /**
     * @var array
     */
    protected $methodNameToPropertyMapping = array(
        'getName' => 'name',
        'isHidden' => 'hidden',
        'isHiddenInIndex' => 'hiddenInIndex',
        'getHiddenBeforeDateTime' => 'hiddenBeforeDateTime',
        'getHiddenAfterDateTime' => 'hiddenAfterDateTime',
        'getAccessRoles' => 'accessRoles',
    );

    /**
     * @return string
     */
    protected function buildMethodPrivilegeMatcher()
    {
        return 'within(' . NodeInterface::class . ') && method(.*->(getProperty|getName|isHidden|getHiddenBeforeDateTime|getHiddenAfterDateTime|isHiddenInIndex|getAccessRoles)())';
    }
}
