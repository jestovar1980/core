<?php
/**
 * @author Jan Ackermann <jackermann@owncloud.com>
 *
 * @copyright Copyright (c) 2021, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Files_Sharing;

use OCP\IConfig;
use OCP\IGroup;
use OCP\IUser;
use OCP\IGroupManager;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class to handle a whitelist for sharing. The main functionality is to check if a particular group
 * has been whitelisted for public sharing, which means that only users of a whitelist should be able to create public shares.
 *
 * Note that this class will only handle the configuration and perform the checks against the configuration.
 * This class won't prevent the sharing action by itself.
 */
class SharingWhitelist {
	/** @var IConfig */
	private $config;

	/** @var IGroupManager */
	private $groupManager;

	public function __construct(IConfig $config, IGroupManager $groupManager) {
		$this->config = $config;
		$this->groupManager = $groupManager;
	}

	/**
	 * Check if the whitelisting is enabled
	 * @return bool true if whitelisting is enabled, false otherwise
	 */
	public function isGroupPublicShareSharersWhitelistEnabled() {
		$configRecord = $this->config->getAppValue('files_sharing', 'whitelisted_public_share_sharers_groups_enabled', 'no');
		return $configRecord === 'yes';
	}

	/**
	 * Check if the target group is whitelisted
	 * @param IGroup $group the group to check
	 * @return bool true if the group is blacklisted, false otherwise
	 */
	public function isGroupPublicShareSharersWhitelisted(IGroup $group) {
		return \in_array($group->getGID(), $this->fetchWhitelistedPublicShareSharersGroups());
	}

	/**
	 * Set the list of groups to be whitelisted by id.
	 * @param string[] $ids a list with the ids of the groups to be whitelisted
	 */
	public function setWhitelistedPublicShareSharersGroups(array $ids) {
		$this->config->setAppValue('files_sharing', 'whitelisted_public_share_sharers_groups', \json_encode($ids));
	}

	/**
	 * Set whitelisting enabled.
	 * @param bool $enabled the value if enabled or not
	 */
	public function setWhitelistedPublicShareSharersGroupsEnabled(bool $enabled) {
		$this->config->setAppValue('files_sharing', 'whitelisted_public_share_sharers_groups_enabled', $enabled ? 'yes' : 'no');
	}

	/**
	 * Get the list of whitelisted group ids
	 * Note that this might contain wrong information
	 * @return string[] the list of group ids
	 */
	public function getWhiteListedPublicShareSharersGroups() {
		return  $this->fetchWhitelistedPublicShareSharersGroups();
	}

	/**
	 * Check if a given user is in any whitelisted group
	 * @param IUser $user the user to check
	 * @return bool true if the user is in any whitelisted group, false otherwise
	 */
	public function isUserInWhitelistedPublicShareSharersGroups(IUser $user) {
		// Evaluate to true, if the admin enables the settings but don't set any groups.
		if (empty($this->fetchWhitelistedPublicShareSharersGroups())) {
			return true;
		}

		$userGroups = $this->groupManager->getUserGroups($user);

		foreach ($userGroups as $userGroup) {
			if ($this->isGroupPublicShareSharersWhitelisted($userGroup)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool[] an array with group ids
	 *                If the whitelisted groups cannot be parsed as valid JSON,
	 *                then an empty list is returned.
	 */
	private function fetchWhitelistedPublicShareSharersGroups() {
		$configuredWhitelist = $this->config->getAppValue('files_sharing', 'whitelisted_public_share_sharers_groups', '[]');
		$parsedValues = json_decode($configuredWhitelist, true);
		return $parsedValues === null ? [] : $parsedValues;
	}
}
