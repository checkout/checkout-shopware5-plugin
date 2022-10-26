// {namespace name="backend/cko_setup/controller/sepa"}
// {block name="backend/cko_setup/controller/sepa"}
Ext.define('Shopware.apps.CkoSetup.controller.Sepa', {
    extend: 'Enlight.app.Controller',
    refs: [
        { ref: 'configurationTab', selector: 'cko-setup-sepa-configuration' },
        { ref: 'topToolbar', selector: 'cko-setup-top-toolbar' }
    ],

    shopId: null,

    loadConfigurationUrl: '{url controller="CkoSetupSepa" action="loadConfiguration" module=backend}',

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
            'cko-setup-sepa-configuration': {
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
                    me.showNotificationMessage('{s name="notification/growl/configuration/loadConfigurationErrorMessage"}{/s}');
                    me.disableConfigurationTab();
                }

                me.configurationRecord = Ext.create('Shopware.apps.CkoSetup.model.Sepa.Configuration', decodedResponse.configuration);
                me.configurationRecord.set('shopId', shopId);

                configurationTab.loadRecord(me.configurationRecord);
            },
            failure: function () {
                me.showNotificationMessage('{s name="notification/growl/configuration/loadConfigurationErrorMessage"}{/s}');
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

    showNotificationMessage: function (message) {
        Shopware.Notification.createGrowlMessage(
            '{s name="notification/growl/title"}{/s}',
            message
        );
    }
});
// {/block}
