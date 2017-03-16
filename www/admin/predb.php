<?php
require_once realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'smarty.php');

use app\models\Predb;

$page = new AdminPage();
$page->title = "Browse PreDb";
$page->meta_title = "View PreDb info";
$page->meta_keywords = "view,predb,info,description,details";
$page->meta_description = "View PreDb info";

$predb = new PreDb();

$offset = (isset($_REQUEST["offset"]) && ctype_digit($_REQUEST['offset'])) ? $_REQUEST["offset"] : 0;

if (isset($_REQUEST['presearch'])) {
	$lastSearch = $_REQUEST['presearch'];
	$parr = $predb->getAll($offset, ITEMS_PER_PAGE, $_REQUEST['presearch']);
} else {
	$lastSearch = '';
	$parr = $predb->getAll($offset, ITEMS_PER_PAGE);
}

// TODO modelise.
$count = $parr['count'];
$page->smarty->assign('lastSearch', $lastSearch);

$page->smarty->assign('results', $parr['arr']);


$pageno = (isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
$page->smarty->assign(
	[
		'pagecurrent'      => (int)$pageno,
		'pagemaximum'      => (int)($count / ITEMS_PER_PAGE) + 1,
		'pager'            => $page->smarty->fetch("pagination.tpl"),
		'pagerquerybase'   => WWW_TOP . "/predb.php?page=",
		'pagerquerysuffix' => '#results',
		'pagertotalitems'  => $count,
	]
);

$count = Predb::findRangeCount($_REQUEST['presearch']);
$lastsearch = isset($_REQUEST['presearch']) ? $_REQUEST['presearch'] : '';

$pageno = (isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
$page->smarty->assign(
	[
		'lastSearch'       => $lastsearch,
		'pagecurrent'      => (int)$pageno,
		'pagemaximum'      => (int)($count / ITEMS_PER_PAGE) + 1,
		'pagerquerybase'   => WWW_TOP . "/predb.php?" . $lastsearch . 'page=',
		'pagerquerysuffix' => '',
		'pagertotalitems'  => $count,
		'results'          => Predb::findRange($pageno, ITEMS_PER_PAGE, $_REQUEST['presearch'])->to('array'),
		'tz'               => \lithium\data\Connections::config()['default']['object']->timezone(),
	]
);
// Pager has to be set outside of main assign, or it will no recieve the scope of those variables.
$page->smarty->assign('pager', $page->smarty->fetch("paginate.tpl"));

$page->content = $page->smarty->fetch('predb.tpl');
$page->render();

?>
