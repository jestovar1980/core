/**
 * @author Jan Ackermann <jackermann@owncloud.com>
 *
 * @copyright Copyright (c) 2021, ownCloud GmbH
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

(function () {
	OC.Plugins.register('OC.Share.ShareDialogView', {
		attach: function (view) {
			var that = this;
			var baseRenderCall = view.render;

			view.render = function () {
				baseRenderCall.call(view);

				if(that.isPublicSharingForbiddenByWhitelist()){
					view.$el.find('.subtab-publicshare').remove();
				}
			};
		},

		isPublicSharingForbiddenByWhitelist: function () {
			var whitelistingEnabled = oc_appconfig.files_sharing.whitelistedPublicShareSharersGroupsEnabled;
			var whitelistingGroups = oc_appconfig.files_sharing.whitelistedPublicShareSharersGroups;

			if (whitelistingEnabled === true &&
				whitelistingGroups.length > 0
			) {
				var userGroups = OC.getCurrentUser().groups;

				for (var i = 0; i < userGroups.length; i++) {
					if (whitelistingGroups.includes(userGroups[i])) {
						return false;
					}
				}

				return true;
			}

			return false;
		}

	});
})(OCA);
