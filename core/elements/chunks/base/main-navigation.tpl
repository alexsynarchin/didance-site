
<nav class="main-nav">
    <section class="main-nav__panel">
        <span class="main-nav__panel-toggle collapsed" data-toggle="collapse" data-target=".main-nav__list" aria-expanded="false">
                <i class="fa fa-bars main-nav__panel-icon" aria-hidden="true"></i>
                <span class="main-nav__panel-text">Меню</span>
        </span>
    </section>
    {$_modx->runSnippet('!pdoMenu@PropertySet', [
    'parents' => 0,
    'level' => 1,
    'tpl' => '@INLINE <li class="main-nav__item"><a href="[[+link]]" class="main-nav__link" [[+attributes]]>[[+menutitle]]</a></li>&nbsp',
    'outerClass' => 'main-nav__list collapse',
    'tplHere' => '@INLINE <li class="main-nav__item main-nav__item--active"><a href="[[+link]]" [[+attributes]] class="main-nav__link main-nav__link--active">[[+menutitle]]</a></li>&nbsp'
    ])}
</nav>