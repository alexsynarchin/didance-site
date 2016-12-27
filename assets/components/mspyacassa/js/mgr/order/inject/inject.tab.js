Ext.override(miniShop2.window.UpdateOrder, {

	mspyacassaOriginals: {
		getTabs: miniShop2.window.UpdateOrder.prototype.getTabs
	},

	getTabs: function (config) {
		var tabs = this.mspyacassaOriginals.getTabs.call(this, config);

		if (!mspyacassa.tools.inArray(config.record.payment, mspyacassa.config.miniShop2.payment.ids)) {
			return tabs;
		}

		tabs.push(this.mspyacassaGetTabs(config));

		return tabs;
	},

	mspyacassaGetTabs: function (config) {
		var tabs = [];

		var add = {
			payment: {
				bodyStyle: 'margin: 1px 0;',
				items: [{
					columnWidth: 1,
					layout: 'form',
					items: [{
						xtype: 'textarea',
						fieldLabel: _('mspyacassa_properties'),
						anchor: '100%',
						msgTarget: 'under',
						name: 'properties[payment]',
						height:'300',
						disabled: true,
						setValue: function(value) {
							MODx.Ajax.request({
								url: miniShop2.config.connector_url,
								params: {
									action: 'mgr/orders/get',
									id: config.record.id
								},
								listeners: {
									success: {
										fn: function (response) {
											value = response.object.properties['payment'] || {};

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
			}
		};

		mspyacassa.config.inject_order_tabs.filter(function (tab) {
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