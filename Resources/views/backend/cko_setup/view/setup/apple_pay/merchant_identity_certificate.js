// {namespace name="backend/cko_setup/view/setup/apple_pay/merchant_identity_certificate"}
// {block name="backend/cko_setup/view/setup/apple_pay/merchant_identity_certificate"}
Ext.define('Shopware.apps.CkoSetup.view.setup.ApplePay.MerchantIdentityCertificate', {
    extend: 'Ext.container.Container',

    alias: 'widget.cko-setup-applepay-merchant-identity-certificate',
    title: '{s name=window/tab/title}{/s}',
    layout: 'fit',
    autoScroll: true,

    initComponent: function () {
        var me = this;

        me.items = [
            me.createFormPanel()
        ];

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
            items: [me.createMainFieldset()]
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
                    me.createCsrCertificateDownloadButton(),
                    me.createCertificateSelectFileField(),
                    me.createPemCertificateInfoText(),
                    me.createGeneratePemCertificateDownloadButton()
                ]
            }]
        });

        return me.mainFieldset;
    },

    createCsrCertificateDownloadButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: '{s name="button/generateCsrCertificate"}{/s}',
            cls: 'primary',
            iconCls: 'sprite-plus-circle',
            padding: '10px 200px',
            margin: '10px 0',
            action: 'start',
            disabled: false,
            handler: function () {
                me.fireEvent('generateCsrCertificate');
            }
        });
    },

    createCertificateSelectFileField: function () {
        var me = this;

        me.addBtn = Ext.create('Ext.form.field.File', {
            emptyText: '{s name=input/label/certificateFile/emptyText}{/s}',
            helpText: '{s name=input/label/certificateFile/helpText}{/s}',
            margin: '5 0 0 2',
            buttonText: '{s name=button/field/label/selectFile}{/s}',
            buttonConfig: {
                cls: Ext.baseCSSPrefix + 'form-mediamanager-btn small secondary',
                iconCls: 'sprite-plus-circle-frame'
            },
            name: 'applePayCertificateFile',
            itemId: 'applePayCertificateSelectFile',
            width: me.configWidth,
            labelWidth: me.configLabelWidth,
            fieldLabel: '{s name=input/label/certificateFile}{/s}'
        });

        return me.addBtn;
    },

    createInfoText: function () {
        return Ext.create('Ext.container.Container', {
            margin: '0 0 20 0',
            html: '<i style="color: grey" >' + '{s name="setup/infoText"}{/s}' + '</i>'
        });
    },

    createPemCertificateInfoText: function () {
        var infoText = Shopware.Notification.createBlockMessage('{s name="setup/pemCertificateInfoText"}{/s}', 'info');

        infoText.style = {
            'color': '#ffffff',
            'font-size': '14px',
            'background-color': '#4AA3DF',
            'text-shadow': '0 0 5px rgba(0, 0, 0, 0.3)',
            'margin': '20px 0'
        };

        return infoText;
    },

    createGeneratePemCertificateDownloadButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: '{s name=button/generatePemCertificate}{/s}',
            cls: 'primary',
            iconCls: 'sprite-plus-circle',
            padding: '10px 200px',
            margin: '10px 0',
            action: 'start',
            disabled: false,
            handler: function (button) {
                me.fireEvent('generatePemCertificate', button);
            }
        });
    },
});
// {/block}
