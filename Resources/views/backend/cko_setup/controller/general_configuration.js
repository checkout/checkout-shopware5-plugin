// {namespace name="backend/cko_setup/controller/general_configuration"}
// {block name="backend/cko_setup/controller/general_configuration"}
Ext.define('Shopware.apps.CkoSetup.controller.GeneralConfiguration', {
    extend: 'Enlight.app.Controller',
    refs: [
        { ref: 'configurationTab', selector: 'cko-setup-general-configuration' },
        { ref: 'topToolbar', selector: 'cko-setup-top-toolbar' }
    ],

    shopId: null,

    registerWebhooksUrl: '{url controller="CkoSetupGeneralConfiguration" action="registerWebhooks" module=backend}',
    checkApiCredentialsUrl: '{url controller="CkoSetupGeneralConfiguration" action="checkApiCredentials" module=backend}',
    loadConfigurationUrl: '{url controller="CkoSetupGeneralConfiguration" action="loadConfiguration" module=backend}',

    configurationRecord: null,

    init: function() {
        var me = this;

        me.registerCustomEventListeners();

        me.callParent(arguments);
    },

    registerCustomEventListeners: function () {
        var me = this;

        me.control({
            'cko-setup-top-toolbar': {
                changeShop: me.onChangeShop
            },
            'cko-setup-general-configuration': {
                checkApiCredentials: me.onClickCheckApiCredentialsButton,
                registerWebhooks: me.onClickRegisterWebhooksButton,
                saveConfiguration: me.onClickSaveConfigurationButton
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
                    me.showNotificationMessage('{s name=notification/growl/configuration/loadConfigurationErrorMessage}{/s}');
                    me.disableConfigurationTab();
                }

                me.configurationRecord = Ext.create('Shopware.apps.CkoSetup.model.GeneralConfiguration.Configuration', decodedResponse.configuration);
                me.configurationRecord.set('shopId', shopId);

                configurationTab.loadRecord(me.configurationRecord);
            },
            failure: function () {
                me.showNotificationMessage('{s name=notification/growl/configuration/loadConfigurationErrorMessage}{/s}');
            }
        });
    },

    disableConfigurationTab: function () {
        var me = this;

        me.getConfigurationTab().setDisabled(true);
    },

    onChangeShop: function (record) {
        this.shopId = record.get('id');

        this.loadConfiguration(this.shopId);
    },

    onClickCheckApiCredentialsButton: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.checkApiCredentialsUrl,
            method: 'POST',
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);

                me.showNotificationMessage(decodedResponse.message);
            },
            failure: function () {
                if (response.status === 404) {
                    me.showNotificationMessage('{s name=notification/growl/pluginNotActivatedErrorMessage}{/s}');
                } else {
                    me.showNotificationMessage('{s name=notification/growl/checkApiCredentials/errorMessage}{/s}');
                }
            }
        });
    },

    onClickRegisterWebhooksButton: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.registerWebhooksUrl,
            method: 'POST',
            params: {
                shopId: this.shopId
            },
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);

                me.showNotificationMessage(decodedResponse.message);
            },
            failure: function () {
                if (response.status === 404) {
                    me.showNotificationMessage('{s name=notification/growl/pluginNotActivatedErrorMessage}{/s}');
                } else {
                    me.showNotificationMessage('{s name=notification/growl/registerWebhook/registrationErrorMessage}{/s}');
                }
            }
        });
    },

    onClickSaveConfigurationButton: function () {
        var configurationTab = this.getConfigurationTab(),
            configurationTabForm = configurationTab.getForm(),
            configurationValues = configurationTabForm.getValues();

        if (!configurationTabForm.isValid()) {
            this.showNotificationMessage('{s name=notification/growl/configuration/formValidationErrorMessage}{/s}');

            return;
        }

        this.configurationRecord.set(configurationValues);
        this.configurationRecord.save();

        this.showNotificationMessage('{s name=notification/growl/configuration/saveSuccessfulMessage}{/s}');
    },

    showNotificationMessage: function (message) {
        Shopware.Notification.createGrowlMessage(
            '{s name=notification/growl/title}{/s}',
            message
        );
    }
});
// {/block}
