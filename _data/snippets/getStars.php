id: 18
source: 1
name: getStars
category: Tickets
properties: 'a:2:{s:5:"class";a:7:{s:4:"name";s:5:"class";s:4:"desc";s:18:"tickets_prop_class";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:6:"Ticket";s:7:"lexicon";s:18:"tickets:properties";s:4:"area";s:0:"";}s:3:"tpl";a:7:{s:4:"name";s:3:"tpl";s:4:"desc";s:16:"tickets_prop_tpl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:18:"tickets:properties";s:4:"area";s:0:"";}}'
static_file: core/components/tickets/elements/snippets/snippet.get_stars.php

-----

/** @var array $scriptProperties */
if (empty($class)) {
    $class = 'Ticket';
}
/** @var integer $user */
if (empty($user)) {
    $user = $modx->user->get('id');
}
unset($scriptProperties['user']);

$ids = array();
$q = $modx->newQuery('TicketStar', array('class' => $class, 'createdby' => $user));
$q->select('id');
$tstart = microtime(true);
if ($q->prepare() && $q->stmt->execute()) {
    $modx->queryTime = microtime(true) - $tstart;
    $modx->executedQueries++;

    $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
}

if (empty($ids)) {
    return false;
}

$where = array($class . '.id:IN' => $ids);
foreach (array('where') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$scriptProperties['where'] = json_encode($where);
if (empty($parents)) {
    $scriptProperties['parents'] = 0;
}
if (empty($tpl)) {
    unset($scriptProperties['tpl']);
}

return $class == 'Ticket'
    ? $modx->runSnippet('getTickets', $scriptProperties)
    : $modx->runSnippet('getComments', $scriptProperties);