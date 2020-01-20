//<?php
/**
 * WidgetImport
 *
 * WidgetImport
 *
 * @category    plugin
 * @internal    @events OnManagerWelcomeHome
 * @internal    @modx_category Импорт
 * @internal    @properties 
 * @internal    @disabled 0
 * @internal    @installset base
 */
$widgets['import'] = array(
'menuindex' =>'-1',
'id' => 'import',
'cols' => 'col-sm-12',
'icon' => 'fa-download',
'title' => 'Импорт',
'body' => '<div class="card-body">'.$modx->runSnippet('iexport').'</div>',
'hide'=>'0'
);
$modx->Event->output(serialize($widgets));
