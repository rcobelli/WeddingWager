<?php

include '../init.php';

$config['type'] = Rybel\backbone\LogStream::console;

if (!$authHelper->isLoggedIn()) {
    header("Location: login.php");
    die();
}

// Boilerplate
$page = new Rybel\backbone\page();
$page->addHeader("../includes/header.php");
$page->addFooter("../includes/footer.php");
$page->addHeader("../includes/navbar.php");

// Start rendering the content
ob_start();
$betHelper = new BetHelper($config);
$userHelper = new UserHelper($config);

?>
<div class="alert alert-success" role="alert">
<p><?php echo $_SESSION['username'] ?>, your available balance is <span class="money-amount">₡<?php echo number_format($userHelper->getBalanceForUser($_SESSION['id']), 2); ?></span></p>
<p><i>(₡ = CobelliCoin, a fake crypto <s>for Ryan to get rich quick</s> this event)</i></p>
</div>
<div class="alert alert-info" role="alert">
<p>A <b>-135</b> favorite means you must risk ₡135 to gain ₡100</p>
<p>A <b>+350</b> underdog means you risk only ₡100, but you gain ₡350</p>
</div>
<a class="btn btn-warning btn-lg btn-block mb-4" href="leaderboard.php" role="button">Check Out The Leaderboard!</a>


<?php if ($_SESSION['admin']): ?>
<button type="button" class="btn btn-outline-info float-right" onclick="window.open('newBet.php', 'New Bet', 'width=500,height=700')">+</button>
<h2>Administer Bets</h2>
<div class="card-deck-container">
    <?php
    foreach ($betHelper->getAllBets() as $bet) {
        echo '<div class="card">';
            echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $bet['title'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">' . $bet['type'] . '</h6>';
                echo '<p class="lead">' . $bet['description'] . '</p>';

        if (empty($bet['winner'])) {
            if (!empty($bet['closes'])) {
                $timeDifference = strtotime($bet['closes']) - time();
                if ($timeDifference > 0) {
                    // If there is still time left, calculate days, hours, minutes, seconds
                    $days = floor($timeDifference / (60 * 60 * 24));
                    $hours = floor(($timeDifference % (60 * 60 * 24)) / (60 * 60));
                    $minutes = floor(($timeDifference % (60 * 60)) / 60);
    
                    // Format the remaining time in a human-readable way
                    echo '<p class="badge badge-info d-block">Closes in ' . sprintf("%02d days %02d:%02d", $days, $hours, $minutes) . '</p>';
                } else {
                    echo '<p class="badge badge-warning d-block">CLOSED</p>';
                }
            }
            echo '</div>';
            echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item"><button type="button" class="btn btn-secondary btn-block" onclick="window.open(\'updateBet.php?id=' . $bet['id'] . '\', \'Edit Bet\', \'width=500,height=700\')">Update Bet</button></li>';
            echo '</ul>';
        } else {
            echo '<p class="badge badge-info d-block">Outcome: ' . $bet['winner'] . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>
</div>
<?php endif; ?>
<h2>Available Bets</h2>
<div class="card-deck-container">
<?php
$availableBets = $betHelper->getAllAvailableBetsForUser($_SESSION['id']);

if (count($availableBets) == 0) {
    echo "<h5><i>No available bets at this time</i></h5>";
} else {
    foreach ($availableBets as $bet) {
        echo '<div class="card">';
            echo '<div class="card-body pb-1">';
                echo '<h5 class="card-title">' . $bet['title'] . '</h5>';
                if ($bet['type'] == 'Over/Under') {
                    echo '<h6 class="card-subtitle mb-2 text-muted"><b>Over/Under:</b> Guess if the actual answer is above or below the given line</h6>';
                } else if ($bet['type'] == 'Hit (Yes/No)') {
                    echo '<h6 class="card-subtitle mb-2 text-muted"><b>Hit (Yes/No):</b> Only place this wager if you think the event will happen</h6>';
                } else {
                    echo '<h6 class="card-subtitle mb-2 text-muted"><b>Quick Pick:</b> Guess which answer will be correct</h6>';
                }
                echo '<p class="lead">' . $bet['description'] . '</p>';
    
        if (!empty($bet['closes'])) {
            $timeDifference = strtotime($bet['closes']) - time();
            // If there is still time left, calculate days, hours, minutes, seconds
            $days = floor($timeDifference / (60 * 60 * 24));
            $hours = floor(($timeDifference % (60 * 60 * 24)) / (60 * 60));
            $minutes = floor(($timeDifference % (60 * 60)) / 60);
    
            // Format the remaining time in a human-readable way
            echo '<p class="badge badge-info d-block">Closes in ' . sprintf("%02d days %02d:%02d", $days, $hours, $minutes) . '</p>';
        }
            echo '</div>';
            echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item"><button type="button" class="btn btn-light btn-block" onclick="window.open(\'newWager.php?choice=a&id=' . $bet['id'] . '\', \'New Wager\', \'width=500,height=700\')">' . $bet['a_title'] . ': ' . $bet['a_odds'] . '</button></li>';
                if (!empty($bet['b_title'])) {
                    echo '<li class="list-group-item"><button type="button" class="btn btn-light btn-block" onclick="window.open(\'newWager.php?choice=b&id=' . $bet['id'] . '\', \'New Wager\', \'width=500,height=700\')">' . $bet['b_title'] . ': ' . $bet['b_odds'] . '</button></li>';
                }
                if (!empty($bet['c_title'])) {
                    echo '<li class="list-group-item"><button type="button" class="btn btn-light btn-block" onclick="window.open(\'newWager.php?choice=c&id=' . $bet['id'] . '\', \'New Wager\', \'width=500,height=700\')">' . $bet['c_title'] . ': ' . $bet['c_odds'] . '</button></li>';
                }
                if (!empty($bet['d_title'])) {
                    echo '<li class="list-group-item"><button type="button" class="btn btn-light btn-block" onclick="window.open(\'newWager.php?choice=d&id=' . $bet['id'] . '\', \'New Wager\', \'width=500,height=700\')">' . $bet['d_title'] . ': ' . $bet['d_odds'] . '</button></li>';
                }
            echo '</ul>';
        echo '</div>';
    }
}
?>
</div>
<h2>Pending Wagers</h2>
<div class="card-deck-container">
<?php
$wagers = $betHelper->getWagersForUserWithOutcome($_SESSION['id'], 'Pending');

if (count($wagers) == 0) {
    echo "<h5><i>No pending wagers at this time; you should place a bet</i> 😵‍💫</h5>";
} else {
    foreach ($wagers as $wager) {
        echo '<div class="card">';
            echo '<div class="card-body pb-1">';
                echo '<h5 class="card-title">' . $wager['title'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">' . $wager['type'] . '</h6>';
                echo '<p class="lead">₡' . $wager['amount'] . ' on ' . $wager['selection'] . ' at ' . $wager['odds'] . ' odds</p>';
            echo '</div>';
            echo '<div class="card-footer text-muted">';
                echo "<p>Placed at: " . date('h:m a', strtotime($wager['timestamp'])) . '</p>';
                echo "<p>Potential Payout: ₡" . number_format($betHelper->calculatePayout($wager['odds'], $wager['amount']), 2) . '</p>';
            echo '</div>';
        echo '</div>';
    }
}
?>
</div>
<h2>Settled Wagers</h2>
<div class="card-deck-container">
<?php
$wagers = $betHelper->getWagersForUserWithOutcome($_SESSION['id'], 'Winner');

foreach ($wagers as $wager) {
    echo '<div class="card">';
        echo '<div class="card-body pb-1">';
            echo '<h5 class="card-title">' . $wager['title'] . '</h5>';
            echo '<h6 class="card-subtitle mb-2 text-muted">' . $wager['type'] . '</h6>';
            echo '<p class="lead">₡' . $wager['amount'] . ' on ' . $wager['selection'] . ' at ' . $wager['odds'] . ' odds</p>';
            echo '<p class="badge badge-success d-block">WINNER</p>';
        echo '</div>';
        echo '<div class="card-footer text-muted">';
            echo "<p>Paid: ₡" . number_format($betHelper->calculatePayout($wager['odds'], $wager['amount']), 2) . '</p>';
        echo '</div>';
    echo '</div>';
}

$wagers = $betHelper->getWagersForUserWithOutcome($_SESSION['id'], 'Loser');

foreach ($wagers as $wager) {
    echo '<div class="card">';
        echo '<div class="card-body pb-1">';
            echo '<h5 class="card-title">' . $wager['title'] . '</h5>';
            echo '<h6 class="card-subtitle mb-2 text-muted">' . $wager['type'] . '</h6>';
            echo '<p class="lead">₡' . $wager['amount'] . ' on ' . $wager['selection'] . ' at ' . $wager['odds'] . ' odds</p>';
            echo '<p class="badge badge-danger d-block">LOSER</p>';
        echo '</div>';
    echo '</div>';
}

$wagers = $betHelper->getWagersForUserWithOutcome($_SESSION['id'], 'Void');

foreach ($wagers as $wager) {
    echo '<div class="card">';
        echo '<div class="card-body pb-1">';
            echo '<h5 class="card-title">' . $wager['title'] . '</h5>';
            echo '<h6 class="card-subtitle mb-2 text-muted">' . $wager['type'] . '</h6>';
            echo '<p class="lead">₡' . $wager['amount'] . ' on ' . $wager['selection'] . ' at ' . $wager['odds'] . ' odds</p>';
            echo '<p class="badge badge-warning d-block">VOIDED</p>';
        echo '</div>';
        echo '<div class="card-footer text-muted">';
            echo "<p>Refunded: ₡" . $wager['amount'] . '</p>';
        echo '</div>';
    echo '</div>';
}
?>
</div>
<?php
$content = ob_get_clean();
$page->render($content);
