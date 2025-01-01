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

$betHelper = new BetHelper($config);
$userHelper = new UserHelper($config);

if (!empty($_POST)) {
    if ($betHelper->updateBet($_GET['id'], $_POST, $userHelper)) {
        echo "<script>window.close();window.opener.location.reload();</script>";
    } else {
        $page->addError($betHelper->getErrorMessage());
    }
}

// Start rendering the content
ob_start();

$data = $betHelper->getBetById($_GET['id']);

if (!empty($data['closes'])) {
    $data['closes'] = date('H:i', strtotime($data['closes']));
}

if (empty($data['b_title'])) {
    $data['b_title'] = 'Miss';
}

include '../components/updateBetForm.html';

$content = ob_get_clean();
$page->render($content);
