/*
 * Copyright (c) 2018
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

$(document).ready(function() {
	var $blacklistedGroups = $('#files_sharing input[name="blacklisted_receiver_groups"]');
	OC.Settings.setupGroupsSelect($blacklistedGroups);
	$blacklistedGroups.change(function(ev) {
		var groups = ev.val || [];
		groups = JSON.stringify(groups);
		OC.AppConfig.setValue('files_sharing', $(this).attr('name'), groups);
	});

	var $whitelistedPublicShareSharersGroups = $('input[name="whitelisted_public_share_sharers_groups"]');
	var $whiteListedPublicShareSharersGroupsEnabled = $('#whiteListedPublicShareSharersGroupsEnabled');
	OC.Settings.setupGroupsSelect($whitelistedPublicShareSharersGroups);

	$whitelistedPublicShareSharersGroups.change(function(ev) {
		var groups = ev.val || [];
		groups = JSON.stringify(groups);
		OC.AppConfig.setValue('files_sharing', 'whitelisted_public_share_sharers_groups', groups);

	});
	$whiteListedPublicShareSharersGroupsEnabled.change(function() {
		$("#setWhiteListedPublicShareSharersGroups").toggleClass('hidden', !this.checked);
		OC.AppConfig.setValue('files_sharing', 'whitelisted_public_share_sharers_groups_enabled', this.checked ? 'yes' : 'no');

	});
	// Move setting to sharing section
	$whitelistedPublicShareSharersGroups.closest('p').detach().insertAfter($('#shareapiExcludeGroups').closest('p'));
});