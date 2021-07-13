<?php
/**
 * @author Juan Pablo Villafáñez <jvillafanez@solidgear.es>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
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
script('files_sharing', 'settings');
?>

<div class="section" id="files_sharing">
	<h2 class="app-name"><?php p($l->t('Group Sharing Blacklist')); ?></h2>
	<div class="indent">
		<p><?php p($l->t('Exclude groups from receiving shares')); ?></p>
		<input name="blacklisted_receiver_groups" class="noautosave" value="<?php p($_['blacklistedReceivers']) ?>" style="width: 400px"/>
		<br />
		<em><?php p($l->t('These groups will not be available to share with. Members of the group are not restricted in initiating shares and can receive shares with other groups they are a member of as usual.')); ?></em>
	</div>

	<p>
		<input type="checkbox"
			   name="whitelisted_public_share_sharers_groups_enabled"
			   id="whiteListedPublicShareSharersGroupsEnabled" class="checkbox"
			   value="1" <?php if ($_['whiteListedPublicShareSharersGroupsEnabled'] === 'yes') {
	print_unescaped('checked="checked"');
} ?> />
		<label for="whiteListedPublicShareSharersGroupsEnabled"><?php p($l->t('Only certain groups are allowed to create public links')); ?></label><br/>
		<span id="setWhiteListedPublicShareSharersGroups"
			  class="indent <?php if ($_['whiteListedPublicShareSharersGroupsEnabled'] === 'no') {
	p('hidden');
} ?>">
		  <input name="whitelisted_public_share_sharers_groups"
				 id="whiteListedPublicShareSharersGroups"
				 class="noautosave"
				 value="<?php p($_['whiteListedPublicShareSharersGroups']) ?>"
				 style="width: 400px"/>
		  </span>
		</p>
</div>