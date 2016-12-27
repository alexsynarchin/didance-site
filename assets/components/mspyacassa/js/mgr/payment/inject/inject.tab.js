Ext.override(miniShop2.window.UpdatePayment, {

	mspyacassaOriginals: {
		getFields: miniShop2.window.UpdatePayment.prototype.getFields
	},

	getFields: function (config) {
		var fields = this.mspyacassaOriginals.getFields.call(this, config);

		if (!mspyacassa.tools.inArray(config.record.id, mspyacassa.config.miniShop2.payment.ids)) {
			return fields;
		}
		
		var tabs = this.mspyacassaGetTabs(config);

		fields.filter(function (row) {
			if (row.xtype == 'modx-tabs') {
				row.items.push(tabs);
			}
		});

		return fields;

	},

	mspyacassaGetTabs: function (config) {
		var tabs = [];

		var add = {
			add: {
				bodyStyle: 'margin: 5px 0;',
				items: [{
					layout: 'column',
					items: [{
						columnWidth: 1,
						layout: 'form',
						defaults: {msgTarget: 'under', anchor: '100%'},
						items: [/*{
							xtype: 'xcheckbox',
							hideLabel: true,
							boxLabel: _('mspyacassa_properties'),
							name: '_properties',
							checked: false,
							listeners: {
								check: mspyacassa.tools.handleChecked,
								afterrender: mspyacassa.tools.handleChecked
							}
						},*/ {
							xtype: 'textarea',
							fieldLabel: _('mspyacassa_properties'),
							msgTarget: 'under',
							name: 'properties',
							height:'110',
							allowBlank: true,
							setValue: function(value) {
								MODx.Ajax.request({
									url: miniShop2.config.connector_url,
									params: {
										action: 'mgr/settings/payment/get',
										id: config.record.id
									},
									listeners: {
										success: {
											fn: function (response) {
												value = response.object.properties || {};
												return Ext.form.TextField.superclass.setValue.call(this, Ext.util.JSON.encode(value));
											},
											scope: this
										},
										failure: {
											fn: function (response) {
												value = {};
												return Ext.form.TextField.superclass.setValue.call(this, Ext.util.JSON.encode(value));
											},
											scope: this
										}
									}
								});
							}
						}]
					}]
				}]
			}
		};

		mspyacassa.config.inject_payment_tabs.filter(function (tab) {
			if (add[tab]) {
				Ext.applyIf(add[tab], {
					title: _('mspyacassa_tab_' + tab)
				});
				tabs.push(add[tab]);
			}
		});

		return tabs;
	}

});