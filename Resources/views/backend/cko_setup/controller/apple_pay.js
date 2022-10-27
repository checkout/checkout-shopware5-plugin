// {namespace name="backend/cko_setup/controller/apple_pay"}
// {block name="backend/cko_setup/controller/apple_pay"}
Ext.define('Shopware.apps.CkoSetup.controller.ApplePay', {
    extend: 'Enlight.app.Controller',
    refs: [
        { ref: 'merchantIdentityCertificateTab', selector: 'cko-setup-applepay-merchant-identity-certificate' },
        { ref: 'configurationTab', selector: 'cko-setup-applepay-configuration' },
        { ref: 'topToolbar', selector: 'cko-setup-top-toolbar' }
    ],

    shopId: null,

    checkRequirementsUrl: '{url controller="CkoSetupApplePay" action="checkRequirements" module=backend}',
    loadConfigurationUrl: '{url controller="CkoSetupApplePay" action="loadConfiguration" module=backend}',
    uploadDomainVerifyFileUrl: '{url controller="CkoSetupApplePay" action="uploadDomainVerifyFile" module=backend}',
    generateCsrCertificateUrl: '{url controller="CkoSetupApplePay" action="generateCsrCertificate" module=backend}',
    generatePemCertificateUrl: '{url controller="CkoSetupApplePay" action="generatePemCertificate" module=backend}',

    configurationRecord: null,

    init: function() {
        var me = this;

        me.registerCustomEventListeners();
        me.checkRequirements();

        me.callParent(arguments);
    },

    registerCustomEventListeners: function () {
        var me = this;

        me.control({
            'cko-setup-top-toolbar': {
                changeShop: me.onChangeShop
            },
            'cko-setup-applepay-merchant-identity-certificate': {
                generateCsrCertificate: me.onClickGenerateCsrCertificateButton,
                generatePemCertificate: me.onClickGeneratePemCertificateButton
            },
            'cko-setup-applepay-domain-verify button[action=cko-setup-applepay-upload-domain-verify-file-button]': {
                click: me.onClickUploadDomainVerifyFileButton
            },
            'cko-setup-applepay-configuration': {
                saveConfiguration: me.onClickSaveConfigurationButton
            }
        });
    },

    checkRequirements: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.checkRequirementsUrl,
            method: 'POST',
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);

                if (!decodedResponse.success) {
                    me.showNotificationMessage(decodedResponse.message);
                    me.disableMerchantIdentityCertificateTab();
                }
            },
            failure: function () {
                me.showNotificationMessage('{s name="notification/growl/domainVerifyFile/openSslNotAvailableMessage"}{/s}');
            }
        });
    },

    loadConfiguration: function (shopId) {
        var me = this,
            configurationTab = me.getConfigurationTab();

        Ext.Ajax.request({
            url: this.loadConfigurationUrl,
            method: 'POST',
            params: {
                shopId: shopId
            },
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);

                if (!decodedResponse.success) {
                    me.showNotificationMessage('{s name="notification/growl/configuration/loadConfigurationErrorMessage"}{/s}');
                    me.disableConfigurationTab();
                }

                me.configurationRecord = Ext.create('Shopware.apps.CkoSetup.model.ApplePay.Configuration', decodedResponse.configuration);
                me.configurationRecord.set('shopId', shopId);

                configurationTab.loadRecord(me.configurationRecord);
            },
            failure: function () {
                me.showNotificationMessage('{s name="notification/growl/configuration/loadConfigurationErrorMessage"}{/s}');
            }
        });
    },

    disableMerchantIdentityCertificateTab: function () {
        var me = this;

        me.getMerchantIdentityCertificateTab().setDisabled(true);
    },

    disableConfigurationTab: function () {
        var me = this;

        me.getConfigurationTab().setDisabled(true);
    },

    onChangeShop: function (record) {
        this.shopId = record.get('id');

        this.loadConfiguration(this.shopId);
    },

    onClickGenerateCsrCertificateButton: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.generateCsrCertificateUrl,
            method: 'POST',
            params: {
                shopId: this.shopId
            },
            success: function (response) {
                try {
                    var decodedResponse = Ext.JSON.decode(response.responseText);

                    if (!decodedResponse.success) {
                        me.showNotificationMessage('{s name="notification/growl/certificateFile/unableToGenerateCertificateMessage"}{/s}');
                    }
                } catch (exception) {
                    // response seems to successful containing the certificate but not json
                    // we can redirect to generate certificate url

                    var url = me.generateCsrCertificateUrl + '?shopId=' + me.shopId;
                    window.open(url, '_blank');
                }
            },
            failure: function () {
                me.showNotificationMessage('{s name="notification/growl/certificateFile/unableToGenerateCertificateMessage"}{/s}');
            }
        });
    },

    onClickSaveConfigurationButton: function () {
        var configurationTab = this.getConfigurationTab(),
            configurationTabForm = configurationTab.getForm(),
            configurationValues = configurationTabForm.getValues();

        if (!configurationTabForm.isValid()) {
            this.showNotificationMessage('{s name="notification/growl/configuration/formValidationErrorMessage"}{/s}');

            return;
        }

        this.configurationRecord.set(configurationValues);
        this.configurationRecord.save();

        this.showNotificationMessage('{s name="notification/growl/configuration/saveSuccessfulMessage"}{/s}');
    },

    onClickGeneratePemCertificateButton: function (button) {
        var me = this,
            form = button.up('form').getForm(),
            selectedFile = button.up('form').down('#applePayCertificateSelectFile').getValue();

        if (Ext.isEmpty(selectedFile)) {
            me.showNotificationMessage('{s name="notification/growl/certificateFile/noFileSelectedMessage"}{/s}');

            return;
        }

        form.submit({
            url: this.generatePemCertificateUrl + '?shopId=' + this.shopId,
            scope: me,
            failure: function(fp, response) {
                var decodedResponse = response.result;

                me.showNotificationMessage(decodedResponse.message);

                var mask = Ext.get(Ext.getBody().query('.x-mask'));
                mask.hide();
            }
        });
    },

    onClickUploadDomainVerifyFileButton: function (button) {
		var me = this,
			selectedFile = Ext.ComponentQuery.query('[name=domainVerificationFilePathField]')
		var form = selectedFile[0].up('form').getForm()

		if (Ext.isEmpty(selectedFile)) {
			me.showNotificationMessage('{s name="notification/growl/domainVerifyFile/noFileSelectedMessage"}{/s}');

			return;
		}

		form.submit({
			url: this.uploadDomainVerifyFileUrl,
			waitMsg: '{s name="upload/domainVerifyFile/waitMessage"}{/s}',
			scope: me,
			success: function(fp, response) {
				me.showNotificationMessage('{s name="notification/growl/domainVerifyFile/uploadSuccessMessage"}{/s}');

				var mask = Ext.get(Ext.getBody().query('.x-mask'));
				mask.hide();
			},
			failure: function(fp, response) {
				var decodedResponse = response.result;

				me.showNotificationMessage(decodedResponse.message);

				var mask = Ext.get(Ext.getBody().query('.x-mask'));
				mask.hide();
			}
		});
    },

    showNotificationMessage: function (message) {
        Shopware.Notification.createGrowlMessage(
            '{s name="notification/growl/title"}{/s}',
            message
        );
    }
});
// {/block}
