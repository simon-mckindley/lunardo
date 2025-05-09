<?PHP
include_once("tools.php");
$onload = "";
session_unset();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Redirects to the index page if the movie code or session day for that movie are incorrect 
  if ((!array_key_exists($_POST["movie"], $movieList)) ||
    (!array_key_exists($_POST["day"], $movieList[$_POST["movie"]]->sessions))
  ) {
    header("Location: index.php");
  }

  include_once("post-validation.php");
  $fieldErrors = validateBooking();

  // Redirects to index page if errors exist else creates receipt
  if (empty($fieldErrors)) {
    $_SESSION = $_POST;
    $_SESSION["discounted"] = setDiscounted($_SESSION["movie"], $_SESSION["day"]);
    appendFile();
    header("Location: receipt.php");
  } else {
    $_GET["movie"] = $_POST['movie'];
    $onload = "location.href='#form'";
  }
  //
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Redirects to the index page if the movie code is incorrect
  if (!array_key_exists($_GET["movie"], $movieList)) {
    header("Location: index.php");
  }
  //
} else {
  header("Location: index.php");
}

$user = ["name" => "", "email" => "", "mobile" => ""];
$seats = ["STA" => "", "STP" => "", "STC" => "", "FCA" => "", "FCP" => "", "FCC" => ""];
// Assigns error messages to input field arrays
if (isset($_POST["user"])) {
  $user = $_POST["user"];
}
if (isset($_POST["seats"])) {
  $seats = $_POST["seats"];
}

$userError = "";
$seatsError = "";
if (isset($fieldErrors["user"])) {
  foreach ($fieldErrors["user"] as $error) {
    if (isset($error)) {
      $userError .= "*" . $error . "<br>";
    }
  }
}
if (isset($fieldErrors["seats"])) {
  $seatsError = "*" . $fieldErrors["seats"];
}

top_module("Lunardo Booking", $onload);
nav_module("booking");
?>

<main>
  <?php synopsis_module(); ?>

  <section id="form">
    <h2>Booking Details</h2>
    <form id="booking" action="booking.php#form" method="post">
      <input type='hidden' name='movie' value='<?= $_GET["movie"] ?>' />
      <div id="form-grid">

        <fieldset id="user">
          <legend>Contact Information</legend>
          <span class="error-text"><?= $userError ?></span>
          <input type='text' id='user[name]' name='user[name]' value="<?= $user["name"] ?>" pattern="[a-zA-Z .'-]{1,255}" title="Enter your full name with only letters or .'-" placeholder="Full Name" required />
          <input type='email' id='user[email]' name='user[email]' value="<?= $user["email"] ?>" title="Email address" placeholder="Email Address" required />
          <input type='tel' id='user[mobile]' name='user[mobile]' value="<?= $user["mobile"] ?>" pattern="(\(04\)|04|\+614)( ?\d){8}" title="Enter an Australian mobile phone number. eg. 04########" placeholder="Mobile Number" required />
          <!-- ** A4 ** Remember Me checkbox -->
          <input type="checkbox" id="remember-me" onclick="setLocalStorage()">
          <label for="remember-me" id="remember-label" tabindex="0"></label>
        </fieldset>
        <script>
          onload(checkStorage());
        </script>

        <fieldset id="seats">
          <legend>Number of Seats</legend>
          <span class="error-text"><?= $seatsError ?></span>
          <div id="seats-div">
            <!-- Set 'debug' in seatInput_module to 'true' to allow unvalidated text input -->
            <?php
            seatInput_module("std-seats", $seats);
            seatInput_module("fc-seats", $seats);
            ?>
          </div>
        </fieldset>

        <?= sessionOptions_module() ?>

        <div id="submit">
          <button type='submit' id="submit-butt" form="booking" class="button" disabled="true">Submit Booking</button>
          <div id="price-div">
            <span id="price-label">Current Price:</span>
            <span id="price"></span>
          </div>
        </div>

      </div>
    </form>
  </section>
</main>

<?php
end_module();
debug_module();
printMyCode();
?>

</body>

</html>