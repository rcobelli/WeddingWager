<?php

include '../init.php';

$config['type'] = Rybel\backbone\LogStream::console;

if (!$authHelper->isLoggedIn()) {
    die();
}
// Boilerplate
$page = new Rybel\backbone\page();
$page->addHeader("../includes/header.php");
$page->addFooter("../includes/footer.php");

$betHelper = new BetHelper($config);
$userHelper = new UserHelper($config);

if (!empty($_POST)) {
    if ($betHelper->createWager($_POST, $_SESSION['id'], $userHelper)) {
        echo "<script>window.close();window.opener.location.reload();</script>";
    } else {
        $page->addError($betHelper->getErrorMessage());
    }
}

// Start rendering the content
ob_start();

$bet = $betHelper->getBetById($_GET['id']);
$maxBet = $userHelper->getBalanceForUser($_SESSION['id']);

include '../components/newWagerForm.html';

$content = ob_get_clean();
$page->render($content);
