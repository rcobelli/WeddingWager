<?php

include '../init.php';

$config['type'] = Rybel\backbone\LogStream::console;

if (!$authHelper->isAdmin()) {
    die();
}
// Boilerplate
$page = new Rybel\backbone\page();
$page->addHeader("../includes/header.php");
$page->addFooter("../includes/footer.php");

$helper = new BetHelper($config);

if (!empty($_POST)) {
    if ($helper->createBet($_POST)) {
        echo "<script>window.close();window.opener.location.reload();</script>";
    } else {
        $page->addError($helper->getErrorMessage());
    }
}

// Start rendering the content
ob_start();

include '../components/newBetForm.html';

$content = ob_get_clean();
$page->render($content);
