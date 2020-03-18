"use strict";
window.com_tjvendor.UI.Common = {
	generateStates: function (countryId, isAdmin, state, city) {
        var jform_state = jQuery('#jform_region');
        jform_state.find('option').remove().end();
        var jform_city = jQuery('#jform_city');
        jform_city.find('option').remove().end();

        jQuery('#jform_region').prepend(jQuery('<option></option>').html(Joomla.JText._("COM_TJVENDORS_FORM_LIST_SELECT_OPTION")));
        jQuery('#jform_city').prepend(jQuery('<option></option>').html(Joomla.JText._("COM_TJVENDORS_FORM_LIST_SELECT_OPTION")));
        jQuery("#jform_region").trigger("liszt:updated");
        jQuery("#jform_city").trigger("liszt:updated");

        this.generateCitys(countryId, city);

        var country = jQuery("#" + countryId).val();
        var callback = function (error, res) {
            if (error) {
                console.error(error);
            } else if (res.data === undefined || res.data === null || res.data.length <= 0) {
                var op =
                    '<option value="">' + Joomla.JText._("COM_TJVENDORS_FORM_LIST_SELECT_OPTION") + "</option>";
                var select = jQuery("#jform_region");
                select
                    .find("option")
                    .remove()
                    .end();
                select.prepend(op);
                jQuery("#jform_region").trigger("liszt:updated");
            } else {
                com_tjvendor.UI.Common.generateOptions(res.data, countryId, state);
            }
        };

        com_tjvendor.Services.Common.getRegions(country, callback);
    },
    generateCitys: function (countryId, city) {
        var country = jQuery('#' + countryId).val();
        var callback = function (error, res) {
            if (error) {
                console.error(error);
            } else if (res.data === undefined || res.data === null || res.data.length <= 0) {
                var op = '<option value="">' + Joomla.JText._("COM_TJVENDORS_FORM_LIST_SELECT_OPTION") + '</option>';
                var select = jQuery('#jform_city');
                select.find('option').remove().end();
                select.prepend(op);
                jQuery("#jform_city").trigger("liszt:updated");
            } else {
                com_tjvendor.UI.Common.generateOptionsCitys(res.data, countryId, city);
            }
        };

        com_tjvendor.Services.Common.getCitys(country, callback);
    },
    generateOptions: function (data, countryId, state) {
        var index, select, region, op;

        if (countryId == 'jform_country')
        {
            select = jQuery('#jform_region');
            select.find('option').remove().end();
        }

        for (index = 0; index < data.length; ++index)
        {
            region = data[index];
            if (state === region.id)
            {
                op = "<option value=" + region.id + " selected='selected'>" + region.region + '</option>';
            }
            else
            {
                op = "<option value=" + region.id + ">" + region.region + '</option>';
            }

            if (countryId == 'jform_country') {
                jQuery('#jform_region').append(op);
            }

            if (index + 1 == data.length) {
                jQuery("#jform_region").trigger("liszt:updated");
            }
        }

        jQuery("#jform_region").trigger("liszt:updated");
    },

    /* Generate options for city */
    generateOptionsCitys: function (data, countryId, cityDefault) {
        var index, select, city, op;

        if (countryId == 'jform_country') {
            select = jQuery('#jform_city');
            select.find('option').remove().end();
        }

        // Generating options for city
        for (index = 0; index < data.length; ++index)
        {
            city = data[index];

            if (cityDefault == city.id)
            {
                op = "<option value=" + city.id + " selected='selected'>" + city.city + '</option>';
            }
            else
            {
                op = "<option value=" + city.id + ">" + city.city + '</option>';
            }
            if (countryId == 'jform_country') {
                jQuery('#jform_city').append(op);
            }

            if (index + 1 === data.length) {
                jQuery("#jform_city").trigger("liszt:updated");
            }
        }

        jQuery("jform_city").trigger("liszt:updated");
    },
    init: (function() {})()
};
