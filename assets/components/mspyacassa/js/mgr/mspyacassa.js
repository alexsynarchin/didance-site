var mspyacassa = function (config) {
	config = config || {};
	mspyacassa.superclass.constructor.call(this, config);
};
Ext.extend(mspyacassa, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, tools: {}
});
Ext.reg('mspyacassa', mspyacassa);

mspyacassa = new mspyacassa();