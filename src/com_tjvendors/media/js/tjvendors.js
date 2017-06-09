/*
 * @version    SVN: <svn_id>
 * @package    Tjvendors
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

var tjVAdmin =
{
	vendor: {
		/*Initialize event js*/
			initVendorJs: function() {
				jQuery(document).ready(function() {
					tjVAdmin.vendor.generateGatewayFields();
					jQuery(document).on("change","#jform_user_id", function () {
						tjVAdmin.vendor.checkVendor();
					});
					jQuery(document).on("change","#jform_payment_gateway", function () {
						tjVAdmin.vendor.generateGatewayFields();
					});
				});

				Joomla.submitbutton = function (task)
				{
					if(task == 'vendor.apply' || task == 'vendor.save' || task == 'vendor.save2new')
					{
						var username = document.getElementById("jform_user_id").value;

						if(username == 'Select a User.')
						{
							return false;
						}
						else
						{
							Joomla.submitform(task, document.getElementById('vendor-form'));
						}
					}
					else if (task == 'vendor.cancel')
					{
						Joomla.submitform(task, document.getElementById('vendor-form'));
					}
					else
					{
						Joomla.submitform(task, document.getElementById('vendor-form'));
					}
				}
			},
			checkVendor: function() {
				var user=document.getElementById('jform_user_id_id').value;
				var userObject = {};
				userObject["user"] = user;
				userObject["vendor_id"] = vendor_id;
				JSON.stringify(userObject) ;
				jQuery.ajax({
					type: "POST",
					dataType: "json",
					data: userObject,
					url: "index.php?option=com_tjvendors&task=vendor.checkDuplicateUser",
					success:function(data) {
						if(data.vendor_id)
						{
							var error_html = "";
							if(layout === "update")
							{
								error_html += "<br />" + Joomla.JText._('COM_TJVENDOR_DUPLICARE_VENDOR_ERROR');
								jQuery("#system-message-container").html("<div class='alert alert-warning'>" + error_html + "</div>");
								
								return vendorCheck = "exists";
							}
							else
							{
								document.location='index.php?option=com_tjvendors&view=vendor&layout=edit&client='+client+'&vendor_id='+data.vendor_id;
							}
						}
					},
				});
			},
			generateGatewayFields: function(){
				var payment_gateway=document.getElementById('jform_payment_gateway').value;
				var userObject = {};
				userObject["payment_gateway"] = payment_gateway;
				JSON.stringify(userObject) ;
				jQuery.ajax({
					type: "POST",
					dataType: "json",
					data: userObject,
					url: "index.php?option=com_tjvendors&task=vendor.generateGatewayFields",
					success:function(data) {
						jQuery('#payment_details').html(data);
					},
				});
			},
	},
}

var tjVSite =
{
	vendor: {
		/*Initialize event js*/
			initVendorJs: function() {
				jQuery(document).ready(function() {
					tjVSite.vendor.generateGatewayFields();
					jQuery(document).on("change","#jform_payment_gateway", function () {
						tjVSite.vendor.generateGatewayFields();
					});
				});

				Joomla.submitbutton = function (task)
				{
					if (task == 'vendor.cancel')
					{
						Joomla.submitform(task, document.getElementById('vendor-form'));
					}
					else
					{
						Joomla.submitform(task, document.getElementById('vendor-form'));
					}
				}
			},

			generateGatewayFields: function(){
				var payment_gateway=document.getElementById('jform_payment_gateway').value;
				var userObject = {};
				userObject["payment_gateway"] = payment_gateway;
				JSON.stringify(userObject) ;
				jQuery.ajax({
					type: "POST",
					dataType: "json",
					data: userObject,
					url: "index.php?option=com_tjvendors&task=vendor.generateGatewayFields",
					success:function(data) {
						jQuery('#payment_details').html(data);
					},
				});
			},
	}
}

