/*
 * @package     TJVendors
 * @subpackage  com_tjvendors
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
'use strict';
/** global: com_tjvendor */
com_tjvendor.Services.Common = new (com_tjvendor.Services.Base.extend({
	getRegionsUrl: window.tjSiteRoot + "index.php?option=com_tjvendors&format=json&task=vendor.getRegion",
    getCitysUrl: window.tjSiteRoot + "index.php?option=com_tjvendors&format=json&task=vendor.getCity",
    config: {
        headers: {}
    },
    response: {
        "success": "",
        "message": ""
    },
    getRegions: function (country, callback) {
        let url;

        if (country) {
            url = this.getRegionsUrl + "&country=" + country;
        } else {
            this.response.success = false;

            this.response.message = Joomla.JText._('COM_TJVENDORS_ERROR_NULL_COUNTRY');
            callback(this.response);

            return false;
        }

        return this.get(url, this.config, callback);
    },
    getCitys: function (country, callback) {
        let url;

        if (country) {
            url = this.getCitysUrl + "&country=" + country;
        } else {
            this.response.success = false;
            this.response.message = Joomla.JText._('COM_TJVENDORS_ERROR_NULL_COUNTRY');
            callback(this.response);

            return false;
        }

        return this.get(url, this.config, callback);
    }    
}));
