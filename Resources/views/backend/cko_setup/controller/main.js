// {namespace name="backend/cko_setup/controller/main"}
// {block name="backend/cko_setup/controller/main"}
Ext.define('Shopware.apps.CkoSetup.controller.Main', {
    extend: 'Ext.app.Controller',

    mainWindow: null,

    init: function() {
        var me = this;

        me.createMainWindow();

        me.callParent(arguments);
    },

    createMainWindow: function () {
        var me = this;
        me.mainWindow = me.getView('Window').create({}).show();
    }
});
// {/block}