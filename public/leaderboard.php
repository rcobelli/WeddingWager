<?php

include '../init.php';

$config['type'] = Rybel\backbone\LogStream::console;

// Boilerplate
$page = new Rybel\backbone\page();
$page->addHeader("../includes/header.php");
$page->addFooter("../includes/footer.php");
$page->addHeader("../includes/navbar.php");

// Start rendering the content
ob_start();
$userHelper = new UserHelper($config);

?>
<a class="btn btn-info btn-lg btn-block mb-4" onclick="history.back()" role="button">Back</a>


<h2>Leaderboard</h2>
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Balance</th>
      <th scope="col">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
<?php
$users = $userHelper->getLeaderboard();
$count = 1;
foreach ($users as $user) {
    echo "<tr>";
      echo "<th scope='row'>" . $count . "</th>";
      echo "<td>" . $user['name'] . "</td>";
      echo "<td>" . number_format($user['balance'], 2) . " ₡</td>";
      if ($user['id'] == $_SESSION['id']) {
        echo "<td><span class='badge badge-secondary'>You</span></td>";
      } else {
        echo "<td></td>";
      }
    echo "</tr>";
    $count++;
}
?>
</tbody>
</table>
</div>
<?php
$content = ob_get_clean();
$page->render($content);
