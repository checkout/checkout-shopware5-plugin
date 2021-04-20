// {namespace name="backend/cko_setup/view/setup/apple_pay/domain_verify"}
// {block name="backend/cko_setup/view/setup/apple_pay/domain_verify"}
Ext.define('Shopware.apps.CkoSetup.view.setup.ApplePay.DomainVerify', {
    extend: 'Ext.container.Container',

    alias: 'widget.cko-setup-applepay-domain-verify',
    title: '{s name=window/tab/title}{/s}',
    layout: 'fit',
    autoScroll: true,

	uploadDomainVerifyFileUrl: '{url controller="CkoSetupApplePay" action="uploadDomainVerifyFile" module=backend}',
	getDomainRootPath: '{url controller="CkoSetupApplePay" action="getDomainRootPath" module=backend}',

    initComponent: function () {
        var me = this;

        me.items = [
            me.createFormPanel()
        ];

		me.fillVerificationTextField();
        me.callParent(arguments);

    },

    configWidth: 500,
    configLabelWidth: 150,

    createFormPanel: function () {
        var me = this;

        me.formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 15,
            border: 0,
            autoScroll: true,
            defaults: {
                labelStyle: 'font-weight: 700; text-align: right;'
            },
            items: [me.createMainFieldset()],
        });

        return me.formPanel;
    },

    createMainFieldset: function () {
        var me = this;

        me.mainFieldset = Ext.create('Ext.form.FieldSet', {
            padding: 12,
            border: false,
            defaults: {
                anchor: '100%',
                labelStyle: 'font-weight: 700; text-align: right;'
            },
            items: [{
                xtype: 'container',
                padding: '0 0 8',
                items: [
                    me.createInfoText(),
					me.createTextField(),
                ]
            }]
        });

        return me.mainFieldset;
    },

	createTextField: function () {
		var me = this,
			id = Ext.id();
		me.textField = Ext.create('Ext.form.FieldSet', {
			columnWidth: 0.5,
			flex: 1,
			title: '{s name=window/tab/title}{/s}',
			items :[
				{
					fieldLabel: '{s name=input/label/domainVerifyFilePath}{/s}',
					labelWidth: 120,
					name: 'domainVerificationFilePathField',
					allowBlank: false,
					xtype: 'textfield',
					anchor: '100%'
				},
				{
					fieldLabel: '{s name=input/label/domainVerifyFile}{/s}',
					multiple: false,
					labelWidth: 120,
					name: 'applePayDomainVerifyFile',
					allowBlank: false,
					xtype: 'filefield',
					anchor: '100%',
					buttonText: '{s name=button/field/label/selectFile}{/s}',
					buttonConfig: {
						cls: Ext.baseCSSPrefix + 'form-mediamanager-btn small secondary',
						iconCls: 'sprite-plus-circle-frame'
					},
				},
				{
					labelWidth: 120,
					name: 'DomainVerificationUploadButton',
					action: 'cko-setup-applepay-upload-domain-verify-file-button',
					text: '{s name=button/uploadDomainVerifyFile}{/s}',
					xtype: 'button',
					anchor: '100%'
				}
			]})

		return me.textField;
	},

	fillVerificationTextField: function () {
		Ext.Ajax.request({
			url: this.getDomainRootPath,
			method: 'GET',
			success: function (response) {
				var decodedResponse = Ext.JSON.decode(response.responseText);

				var textField = Ext.ComponentQuery.query('[name=domainVerificationFilePathField]');
				textField[0].setValue(decodedResponse['path']);
			},
			failure: function () {
				me.showNotificationMessage('fail');
			}
		});
	},

    createDomainVerifySelectFile: function () {
        var me = this;

        me.addBtn = Ext.create('Ext.form.field.File', {
            emptyText: '{s name=input/label/domainVerifyFile}{/s}',
            margin: '5 0 0 2',
            buttonText: '{s name=button/field/label/selectFile}{/s}',
            buttonConfig: {
                cls: Ext.baseCSSPrefix + 'form-mediamanager-btn small secondary',
                iconCls: 'sprite-plus-circle-frame'
            },
            name: 'applePayDomainVerifyFile',
            itemId: 'domainVerifySelectFile',
            width: me.configWidth,
            labelWidth: me.configLabelWidth,
            fieldLabel: '{s name=input/label/domainVerifyFile}{/s}'
        });

        return me.addBtn;
    },

    createInfoText: function () {
        return Ext.create('Ext.container.Container', {
            margin: '0 0 20 0',
            html: '<i style="color: grey" >' + '{s name="setup/descriptionText"}{/s}' + '</i>'
        });
    },

	showNotificationMessage: function (message) {
		Shopware.Notification.createGrowlMessage(
			'{s name=notification/growl/title}{/s}',
			message
		);
	},

});

// {/block}
