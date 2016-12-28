id: 30
source: 1
name: ms2GalleryResources
category: ms2Gallery
properties: 'a:3:{s:10:"typeOfJoin";a:7:{s:4:"name";s:10:"typeOfJoin";s:4:"desc";s:26:"ms2gallery_prop_typeOfJoin";s:4:"type";s:4:"list";s:7:"options";a:2:{i:0;a:2:{s:4:"text";s:4:"left";s:5:"value";s:4:"left";}i:1;a:2:{s:4:"text";s:5:"inner";s:5:"value";s:5:"inner";}}s:5:"value";s:4:"left";s:7:"lexicon";s:21:"ms2gallery:properties";s:4:"area";s:0:"";}s:13:"includeThumbs";a:7:{s:4:"name";s:13:"includeThumbs";s:4:"desc";s:29:"ms2gallery_prop_includeThumbs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:6:"120x90";s:7:"lexicon";s:21:"ms2gallery:properties";s:4:"area";s:0:"";}s:15:"includeOriginal";a:7:{s:4:"name";s:15:"includeOriginal";s:4:"desc";s:31:"ms2gallery_prop_includeOriginal";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:21:"ms2gallery:properties";s:4:"area";s:0:"";}}'
static_file: core/components/ms2gallery/elements/snippets/snippet.ms2gallery_resources.php

-----

/** @var array $scriptProperties */
$class = 'modResource';

// Load model
if (empty($loadModels)) {
	$scriptProperties['loadModels'] = 'ms2Gallery';
} else {
	$loadModels = array_map('trim', explode(',', $loadModels));
	if (!in_array('ms2gallery', $loadModels)) {
		$loadModels[] = 'ms2gallery';
	}
	$scriptProperties['loadModels'] = $loadModels;
}

// Type of join
if (empty($typeOfJoin)) {
	$typeOfJoin = 'left';
}
switch (strtolower(trim($typeOfJoin))) {
	case 'right':
		$join = 'rightJoin';
		break;
	case 'inner':
		$join = 'innerJoin';
		break;
	default:
		$join = 'leftJoin';
		break;
}

// Select modResource
$columns = array_keys($modx->getFieldMeta($class));
if (empty($includeContent) && empty($useWeblinkUrl)) {
	$key = array_search('content', $columns);
	unset($columns[$key]);
}
$select = array(
	$class => implode(',', $columns)
);

// Include Thumbnails
${$join} = array();
if (empty($includeThumbs)) {
	$includeThumbs = '120x90';
}
$thumbs = array_map('trim', explode(',', $includeThumbs));
if (!empty($thumbs[0])) {
	foreach ($thumbs as $thumb) {
		${$join}[] = array(
			'class' => 'msResourceFile',
			'alias' => $thumb,
			'on' => preg_replace('/(\n|\t)/', '', "
				`$thumb`.`resource_id` = `$class`.`id` AND
				`$thumb`.`parent` != 0 AND
				`$thumb`.`path` LIKE '%/$thumb/' AND
				`$thumb`.`active` = 1 AND
				`$thumb`.`rank` = 0
			")
		);
		$select[$thumb] = preg_replace('/(\n|\t)/', '', "
			`$thumb`.`url` as `$thumb`,
			`$thumb`.`name` as `$thumb.name`,
			`$thumb`.`description` as `$thumb.description`,
			`$thumb`.`createdon` as `$thumb.createdon`,
			`$thumb`.`createdby` as `$thumb.createdby`,
			`$thumb`.`properties` as `$thumb.properties`,
			`$thumb`.`alt` as `$thumb.alt`,
			`$thumb`.`add` as `$thumb.add`
		");

		if (!empty($includeOriginal)) {
			${$join}[] = array(
				'class' => 'msResourceFile',
				'alias' => $thumb.'o',
				'on' => preg_replace('/(\n|\t)/', '', "
					`{$thumb}o`.`resource_id` = `$class`.`id` AND
					`{$thumb}o`.`parent` = 0 AND
					`{$thumb}o`.`active` = 1 AND
					`{$thumb}o`.`rank` = 0
				")
			);
			$select[$thumb] .= ", `{$thumb}o`.`url` as `$thumb.original`";
		}
	}
}

foreach (array('leftJoin', 'innerJoin', 'rightJoin', 'select') as $v) {
	if (!empty($scriptProperties[$v])) {
		$tmp = $modx->fromJSON($scriptProperties[$v]);
		if (is_array($tmp)) {
			$$v = array_merge($$v, $tmp);
		}
	}
	unset($scriptProperties[$v]);
}
$scriptProperties['select'] = $modx->toJSON($select);
$scriptProperties['groupby'] = $class . '.id';
$scriptProperties[$join] = $modx->toJSON(${$join});

return $modx->runSnippet('pdoResources', $scriptProperties);