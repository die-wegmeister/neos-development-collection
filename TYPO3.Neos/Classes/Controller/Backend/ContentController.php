<?php
namespace TYPO3\TYPO3\Controller\Backend;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use \TYPO3\TYPO3\Controller\Exception\NodeCreationException;

/**
 * The TYPO3 ContentModule controller
 *
 * @scope singleton
 */
class ContentController extends \TYPO3\FLOW3\MVC\Controller\ActionController {

	/**
	 *
	 * @param TYPO3\TYPO3CR\Domain\Model\Node $referenceNode
	 * @param string $position either "above" or "below"
	 * @return string
	 * @skipCsrfProtection
	 */
	public function newAction($referenceNode, $position) {
		$this->view->assign('referenceNode', $referenceNode);
		$this->view->assign('position', $position);
		$this->view->assign('rand', rand());
	}

	/**
	 * @param TYPO3\TYPO3CR\Domain\Model\Node $referenceNode
	 * @param string $position either "above" or "below"
	 * @param string $type
	 * @return string
	 */
	public function createAction($referenceNode, $position, $type) {
		if (!in_array($position, array('above', 'below', 'inside'))) {
			throw new NodeCreationException(sprintf('Position "%s" given, but only "above, below, inside" supported', $position), 1313754773);
		}

		if ($position === 'inside') {
			$parentNode = $referenceNode;
		} else {
			$parentNode = $referenceNode->getParent();
		}

		// TODO: Write policy which only allows createAction for logged in users!
		// TODO: make it possible for the user to specify the node identifier
		$newNode = $parentNode->createNode(uniqid(), $type);
		if ($position === 'above') {
			$newNode->moveBefore($referenceNode);
		} elseif ($position === 'below') {
			$newNode->moveAfter($referenceNode);
		}
		switch($type) {
			case 'TYPO3.TYPO3:Text':
				$newNode->setProperty('headline', 'Enter Headline here');
				$newNode->setProperty('text', '<p>Enter Text here</p>'); // Wrapping into p-tags here because of Aloha
				break;
			// TODO: Some more here, depending on Schema
		}

		$parentFolderNode = $this->findNextParentFolderNode($newNode);
			// TODO: write Page URI service; it must be easier to retrieve the URI for a node...
		$pageUri = $this->uriBuilder
				->reset()
				->uriFor('show', array('node' => $parentFolderNode), 'Frontend\Node', 'TYPO3.TYPO3');
		return '<a rel="typo3-created-new-content" href="' . $newNode->getContextPath() . '" data-page="' . $pageUri . '">Go to new content element</a>';
	}

	protected function findNextParentFolderNode($node) {
		while ($node = $node->getParent()) {
			if ($node->getContentType() === 'TYPO3.TYPO3:Page') {
				// TODO: Support for other "Folder" types, which are not of type "Page"
				return $node;
			}
		}
		return NULL;
	}
}
?>