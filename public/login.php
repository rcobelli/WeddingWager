<?php

include '../init.php';

$config['type'] = Rybel\backbone\LogStream::console;

$userExists = false;
if (isset($_GET['username'])){ 
    $helper = new UserHelper($config);
    if ($helper->getUser($_GET['username']) === false || $_GET['confirmed']) {
        $response = $helper->loginUser($_GET['username']);
        if ($response !== false) {
            $authHelper->login($_GET['username'], $response['admin'], $response['id']);
        }
    } else {
        $userExists = true;
    }
}

if ($authHelper->isLoggedIn()) {
    header("Location: index.php");
    die();
}

// Boilerplate
$page = new Rybel\backbone\page();
$page->addHeader("../includes/header.php");
$page->addFooter("../includes/footer.php");
$page->addHeader("../includes/navbar.php");

// Start rendering the content
ob_start();
$year = date('Y');

?>
<div>
    <h2>What is this?</h2>
    <p>Bet on the dumbest <span class="colored-highlight">Gen-AI</span> fueled things you can think of throughout the night & see how you stack up against your felow <span class="colored-highlight">betting buffoons</span> & <span class="colored-highlight">high-stakes hooligans</span>.</p>
    <p>Dig up those classic <span class="colored-highlight">dank Kahoot names</span> and give us something to laugh at on the leaderboard</p> 
</div>
<hr/>
<form method="get">
    <h2>Start Playing!</h2>
    <div class="form-group">
        <label for="input1">Name for Leaderboard:</label>
        <input name="username" type="text" class="form-control" id="input1" placeholder="Hannah Bannah" value="<?php echo $_GET['username']; ?>" required  autocomplete="off">
        <small id="help1" class="form-text text-muted">If you've been logged out, enter the same name as before to pick up where you left off</small>
    </div>
    
    <?php
    if (!isset($_GET['username'])) {
        echo '<button type="submit" class="btn btn-dark mt-3" id="submitButton">Validate</button>';
    } else {
        echo "<p>This username already exists! If this wasn't you, please <u><a href='?'>refresh</a></u> the page and try a different username.</p>";
        echo '<input type="hidden" name="confirmed" value="true"/>';
        echo '<input type="hidden" name="test" value="false"/>';
        echo '<button type="submit" class="btn btn-danger" id="submitButton">Submit</button>';
    }
    ?>
</form>
<hr/>
<figure class="figure">
    <img src="resources/logo.jpg" class="figure-img img-fluid rounded" alt="A generic square placeholder image with rounded corners in a figure.">
    <figcaption class="figure-caption">DALL-E 3's interpretation of '<i>Cobelli Wedding Sports book logo</i>'</figcaption>
</figure>
<?php
$content = ob_get_clean();
$page->render($content);
