id: 31
source: 1
name: videoGallery.input
category: videoGallery
properties: 'a:5:{s:2:"tv";a:7:{s:4:"name";s:2:"tv";s:4:"desc";s:20:"videogallery_prop_tv";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:23:"videogallery:properties";s:4:"area";s:0:"";}s:4:"tvId";a:7:{s:4:"name";s:4:"tvId";s:4:"desc";s:22:"videogallery_prop_tvId";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:23:"videogallery:properties";s:4:"area";s:0:"";}s:7:"tvInput";a:7:{s:4:"name";s:7:"tvInput";s:4:"desc";s:25:"videogallery_prop_tvInput";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:23:"videogallery:properties";s:4:"area";s:0:"";}s:3:"res";a:7:{s:4:"name";s:3:"res";s:4:"desc";s:21:"videogallery_prop_res";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:23:"videogallery:properties";s:4:"area";s:0:"";}s:3:"tpl";a:7:{s:4:"name";s:3:"tpl";s:4:"desc";s:21:"videogallery_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:22:"tpl.videoGallery.input";s:7:"lexicon";s:23:"videogallery:properties";s:4:"area";s:0:"";}}'
static_file: core/components/videogallery/elements/snippets/snippet.videoGallery.input.php

-----

$tv			= $modx->getOption('tv', $scriptProperties, ''); // TV name or...
$tvid		= $modx->getOption('tvId', $scriptProperties, ''); // ... TV id
$tvInput	= $modx->getOption('tvInput', $scriptProperties, ''); // TV input name
$res		= $modx->getOption('res', $scriptProperties, 0); // Resource id
$tpl		= $modx->getOption('tpl', $scriptProperties, 'tpl.videoGallery.input');

$tv_where = $tv ? array( 'name' => $tv ) : '';
$tv_where = $tv_where ?: ( $tvid? array( 'id' => $tvid ) : '' );

if( empty($tv_where) ) { return; }


if( $tv_obj = $modx->getObject('modTemplateVar', $tv_where) )
{
	$value = '';
	
	if( $res && $tv_val_obj = $modx->getObject('modTemplateVarResource', array(
			'tmplvarid'	=> $tv_obj->id,
			'contentid'	=> $res,
	))) {
		$value = $tv_val_obj->value;
	}
	
	$return = $modx->getChunk($tpl, array(
		'tv_id'			=> $tv_obj->id,
		'tv_name'		=> $tv_obj->name,
		'tv_input_name'	=> $tvInput ?: $tv_obj->name,
		'tv_value'		=> htmlspecialchars($value),
		'res_id'		=> $res,
	));
	
	return $return;
}
else {
	return;
}