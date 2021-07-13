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
namespace OCA\Files_Sharing\Tests;

use OCP\IConfig;
use OCP\IGroup;
use OCA\Files_Sharing\SharingBlacklist;
use OCA\Files_Sharing\SharingWhitelist;
use OCP\IGroupManager;
use OCP\IUser;

class SharingWhitelistTest extends \Test\TestCase {
	/** @var IConfig | \PHPUnit\Framework\MockObject\MockObject */
	private $config;

	/** @var IGroupManager | \PHPUnit\Framework\MockObject\MockObject */
	private $groupManager;

	/** @var SharingBlacklist | \PHPUnit\Framework\MockObject\MockObject */
	private $sharingWhitelist;

	public function setUp(): void {
		$this->config = $this->getMockBuilder(IConfig::class)
			->disableOriginalConstructor()
			->getMock();

		$this->groupManager = $this->getMockBuilder(IGroupManager::class)
			->disableOriginalConstructor()
			->getMock();

		$this->sharingWhitelist = new SharingWhitelist($this->config, $this->groupManager);
	}

	/**
	 * @dataProvider isGroupPublicShareSharersWhitelistEnabledDataProvider
	 */
	public function testIsGroupPublicShareSharersWhitelistEnabled($config, $result) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap($config));

		$this->assertEquals($this->sharingWhitelist->isGroupPublicShareSharersWhitelistEnabled(), $result);
	}

	public function isGroupPublicShareSharersWhitelistEnabledDataProvider() {
		return [
			[[['files_sharing', 'whitelisted_public_share_sharers_groups_enabled', 'no', 'yes']], true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups_enabled', 'no', 'no']], false ],
		];
	}

	/**
	 * @dataProvider isGroupPublicShareSharersWhitelistedDataProvider
	 */
	public function testIsGroupPublicShareSharersWhitelisted($config, $groupId, $result) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap($config));

		$group = $this->createMock(IGroup::class);
		$group->method('getGID')
			->willReturn($groupId);

		$this->assertEquals($this->sharingWhitelist->isGroupPublicShareSharersWhitelisted($group), $result);
	}

	public function isGroupPublicShareSharersWhitelistedDataProvider() {
		return [
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '[]']], 'admin', false ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2"]']], 'admin', false ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2","admin"]']], 'admin', true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["admin"]']], 'admin', true ],
		];
	}

	/**
	 * @dataProvider isUserInWhitelistedPublicShareSharersGroupsDataProvider
	 */
	public function testIsUserInWhitelistedPublicShareSharersGroups($config, $userGroupIds, $result) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap($config));

		$user = $this->createMock(IUser::class);
		$groups = [];

		foreach ($userGroupIds as $userGroupId) {
			$group = $this->createMock(IGroup::class);
			$group->method('getGID')
				->willReturn($userGroupId);
			$groups[] = $group;
		}

		$this->groupManager->expects($this->any())->method('getUserGroups')
			->willReturn($groups);

		$this->assertEquals($this->sharingWhitelist->isUserInWhitelistedPublicShareSharersGroups($user), $result);
	}

	public function isUserInWhitelistedPublicShareSharersGroupsDataProvider() {
		return [
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '[]']], [], true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '[]']], ['admin'], true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2"]']], [], false ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2"]']], ['admin'], false ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2", "admin"]']], ['admin'], true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2", "admin"]']], ['group1','group2', 'admin'], true ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1","group2", "admin"]']], ['group1','group2', 'group3', 'admin'], true ],
		];
	}

	/**
	 * @dataProvider getWhitelistedPublicShareSharersGroupsDataProvider
	 */
	public function testGetWhitelistedPublicShareSharersGroups($config, $result) {
		$this->config->method('getAppValue')
			->will($this->returnValueMap($config));

		$this->assertEquals($this->sharingWhitelist->getWhitelistedPublicShareSharersGroups(), $result);
	}

	public function getWhitelistedPublicShareSharersGroupsDataProvider() {
		return [
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '']], []],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["invalid JSON missing right square bracket"']], []],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '[]']], [] ],
			[[['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1", "group2"]']], ['group1', 'group2']],
		];
	}

	/**
	 * @dataProvider isGroupPublicShareSharersWhitelistEnabledDataProvider
	 */
	public function testSetWhitelistedPublicShareSharersGroupsEnabled($config, $result) {
		$this->config->method('setAppValue')
			->will($this->returnValueMap($config));

		$this->config->method('getAppValue')
			->will($this->returnValueMap($config));

		$this->assertEquals($this->sharingWhitelist->isGroupPublicShareSharersWhitelistEnabled(), $result);
	}

	public function testSetWhitelistedPublicShareSharersGroups() {
		$this->config->method('setAppValue')
			->will($this->returnValueMap([['files_sharing', 'whitelisted_public_share_sharers_groups', '["group1", "group2"]']]));

		$this->config->method('getAppValue')
			->will($this->returnValueMap([['files_sharing', 'whitelisted_public_share_sharers_groups', '[]', '["group1", "group2"]']]));

		$this->sharingWhitelist->setWhitelistedPublicShareSharersGroups(["group1", "group2"]);
		$this->assertEquals(["group1", "group2"], $this->sharingWhitelist->getWhitelistedPublicShareSharersGroups());
	}
}
