<?php
session_start();

class movie
{
  public $code;
  public $title;
  public $rating;
  public $synop = [];
  public $preview;
  public $sessionsText;
  public $sessions = [];

  function __construct(
    $code,
    $title,
    $rating,
    $synop = ["short" => "", "long" => "", "director" => "", "stars" => ""],
    $preview,
    $sessionsText,
    $sessions = []
  ) {
    $this->code = $code;
    $this->title = $title;
    $this->rating = $rating;
    $this->synop = $synop;
    $this->preview = $preview;
    $this->sessionsText = $sessionsText;
    $this->sessions = $sessions;
  }

  function get_ratingURL()
  {
    return "./media/{$this->rating}rating.png";
  }

  function get_ratingAlt()
  {
    return "{$this->rating} Classification Rating";
  }

  function get_posterURL()
  {
    return "./media/{$this->code}poster.png";
  }
}


// Get movies and seat prices data from json files
$moviesData = json_decode(file_get_contents('./data/movies.json'), true);

$movieList = [];
foreach ($moviesData as $code => $data) {
  $movieList[$code] = new movie(
    $data['code'],
    $data['title'],
    $data['rating'],
    $data['synop'],
    $data['preview'],
    $data['sessionsText'],
    $data['sessions']
  );
}

$seat_prices = json_decode(file_get_contents('./data/seat_prices.json'), true);


$seatText = [
  "STA" => "Standard Adult",
  "STP" => "Standard Concession",
  "STC" => "Standard Child",
  "FCA" => "First Class Adult",
  "FCP" => "First Class Concession",
  "FCC" => "First Class Child",
];

$dayText = [
  "MON" => "Monday",
  "TUE" => "Tuesday",
  "WED" => "Wednesday",
  "THU" => "Thursday",
  "FRI" => "Friday",
  "SAT" => "Saturday",
  "SUN" => "Sunday"
];

function unsetFB(&$str, $fallback = '')     /// General function for setting the value of a string
{
  return (isset($str) ? $str : $fallback);
}

function setChecked(&$str, $val)          /// General function for setting str to "checked" if inputs match
{                                         /// For setting the state of radio buttons
  return (isset($str) && $str == $val ? 'checked' : '');
}

function curr_format($num)      /// General function for converting a number to currency format
{
  return (is_numeric($num)) ? number_format($num, 2, '.', ',') : "";
}

function php2js($arr, $arrName)   /// General function for converting a PHP array to a JS version
{
  $arrJSON = json_encode($arr, JSON_PRETTY_PRINT);
  echo <<<"CDATA"
  <script>
    /* Variable generated with Trev's handy php2js() function */
    var $arrName = $arrJSON;
  </script>\n
CDATA;
}

function setDiscounted($movie, $day)    /// General function to determine if the movie session is discounted
{
  global $movieList;
  $time = $movieList[$movie]->sessions[$day];
  $discounted = "fullprice";

  switch ($day) {
    case "MON":
      $discounted = "discounted";
      break;
    case "TUE":
    case "WED":
    case "THU":
    case "FRI":
      $discounted = ($time == "12pm") ? "discounted" : "fullprice";
      break;
  }

  return $discounted;
}

function top_module($title, $onload = "")   /// Top Module - common
{
  global $seat_prices;
  $styles = ($title == "Booking Receipt") ? "receiptstyle.css" : "style.css";

  echo <<<"OUTPUT"
  <!DOCTYPE html>
  <html lang='en'>

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="Simon Mckindley">
      <meta name="description" content="Assignment 3 for Web Programming">
      <title>$title</title>
      <link rel="icon" href='./media/LunardoLogo.png' type="image/x-icon">

      <link id='stylecss' type="text/css" rel="stylesheet" href='$styles'>
      <script type="text/javascript" src="script.js"></script>
OUTPUT;

  if ($title == "Lunardo Booking") {
    php2js($seat_prices, "seatPrices");
  }
  if ($title == "Lunardo Cinema") {
    echo "\n<script>\n\t\twindow.onscroll = setCurrentLink;\n</script>\n";
  }

  echo "</head>";

  if ($title != "Booking Receipt") {
    echo <<<"NEWPUT"
  <body onload="$onload">
  <header>
      <!-- Original Creative Commons film strip SVG used to create logo attributed to:
      "Nevit Dilmen, CC BY-SA 3.0 <https://creativecommons.org/licenses/by-sa/3.0>, via Wikimedia Commons -->
      <img src='./media/LunardoLogo.png' alt='Lunardo Logo' />
      <h1>Lunardo</h1>
  </header>

NEWPUT;
  }
}

function nav_module($page)   /// Nav Module - common
{
  $indexLinks = [];

  if ($page == "index") {
    $indexLinks = [
      "About Us" => "#about-us",
      "Seats & Prices" => "#seats-prices",
      "Now Showing" => "#now-showing"
    ];
  } elseif ($page == "booking") {
    $indexLinks = [
      "Home" => "index.php",
      "Book Now" => "#form"
    ];
  }

  echo "<nav>\n";
  foreach ($indexLinks as $label => $target) {
    echo "\t<a class='button' href='{$target}'>{$label}</a>\n";
  }
  echo "</nav>\n\n";
}

function end_module()    /// End module - common
{
  echo <<<"OUTPUT"
  <footer>
      <address>
          <p>To contact us please <span>email: <a href='mailto:contactus@lunardo.com.au'>contactus@lunardo.com.au</a></span>
              <span> or phone: <a href='tel:+61414100100'>0414 100 100</a></span><br>
              Come and see us at: <span>123 Short St. Wangaratta Vic. 3677</span>
          </p>
      </address>
      <div>&copy;
          <script>
              document.write(new Date().getFullYear());
          </script>
          Simon Mckindley. Last modified 
OUTPUT;

  echo date('d F Y H:i', filemtime($_SERVER['SCRIPT_FILENAME']));

  echo <<<"NEWPUT"
.</div>
      <div>Disclaimer: This website is not a real website and is being developed as part of a School of Science Web Programming
          course at RMIT University in Melbourne, Australia.</div>
  </footer>

NEWPUT;
}

function debug_module()    /// Debug module - common
{
  global $movieList;
  global $fieldErrors;

  echo "<aside id='debug'>\n<hr>\n";
  echo "\t<h3>Debug Area</h3>\n\t<pre>\n";
  echo "GET Contains:\n";
  print_r($_GET);
  echo "\nPOST Contains:\n";
  print_r($_POST);
  echo "\nSESSION Contains:\n";
  print_r($_SESSION);
  // echo "MovieList Contains:";
  // print_r($movieList);
  echo "\nfielderrors contains:\n";
  print_r($fieldErrors);
  echo "\n\t</pre>\n</aside>\n";
}

function printMyCode()      /// Module to print file code on screen
{
  $allLinesOfCode = file($_SERVER['SCRIPT_FILENAME']);
  echo "<pre id='myCode'><ol>";
  echo "<h3>Page Code</h3>";
  foreach ($allLinesOfCode as $oneLineOfCode) {
    echo "<li>" . rtrim(htmlentities($oneLineOfCode)) . "</li>";
  }
  echo "</ol></pre>";
}

function table_module($type)   /// Seat price table module - Index
{
  global $seat_prices;
  $pre = "";
  $suffix = ["A", "P", "C"];

  if ($type == "Standard") {
    $pre = "ST";
  } elseif ($type == "First Class") {
    $pre = "FC";
  }

  echo <<<"HEAD"

  <table>
    <tr>
      <th>$type Tickets</th>
      <th>Discounted*</th>
      <th>Normal</th>
    </tr>\n
HEAD;

  foreach ($suffix as $suf) {
    $lead = "";
    switch ($suf) {
      case "A": {
          $lead = "Adult";
          break;
        }
      case "P": {
          $lead = "Concession";
          break;
        }
      case "C": {
          $lead = "Child";
          break;
        }
    }
    echo "\t<tr>\n";
    echo "\t\t<td>$lead</td>\n";
    echo "\t\t<td>$" . curr_format($seat_prices['discounted'][$pre . $suf]) . "</td>\n";
    echo "\t\t<td>$" . curr_format($seat_prices['fullprice'][$pre . $suf]) . "</td>\n";
    echo "\t</tr>\n";
  }
  echo "\t</table>\n";
}

function card_module()     /// Card module - Index
{
  global $movieList;
  foreach ($movieList as $movie)
    echo <<<"OUTPUT"

  <div id="$movie->code-card" class="card" tabindex="0">
    <div>
      <img class="poster" src="{$movie->get_posterURL()}" alt="{$movie->title} Movie Poster" />
      <h3 class="f-head">{$movie->title}<img class="rating" src="{$movie->get_ratingURL()}" alt="{$movie->get_ratingAlt()}" /></h3>
      <p class="f-sessions">_ Sessions _<br>{$movie->sessionsText}</p>
      <a class="button" href='booking.php?movie={$movie->code}'>Book Now</a>
    </div>
    <div>
      <img class="back-poster" src="{$movie->get_posterURL()}" alt="{$movie->title} Movie Poster" />
      <h3 class="b-head">{$movie->title}<img class="rating" src="{$movie->get_ratingURL()}" alt="{$movie->get_ratingAlt()}" /></h3>
      <p class="synop">{$movie->synop['short']}</p>
      <p class="b-sessions">_ Sessions _<br>{$movie->sessionsText}</p>
      <a class="button" href='booking.php?movie={$movie->code}'>Book Now</a>
    </div>
  </div>

OUTPUT;
}

function synopsis_module()   /// Synopsis module - Booking
{
  global $movieList;

  $key = $_GET["movie"];

  echo <<<"OUTPUT"
  <div id="movie-title">
    <a href='#synopsis'>
      <h2>{$movieList[$key]->title}</h2>
    </a>
    <img src="{$movieList[$key]->get_ratingURL()}" alt="{$movieList[$key]->get_ratingAlt()}" />
  </div>

  <section id="synopsis">
    <img src="{$movieList[$key]->get_posterURL()}" alt="{$movieList[$key]->title} Movie Poster" />
    <p>{$movieList[$key]->synop['long']}<br><span class="italic">Director:</span> {$movieList[$key]->synop['director']}
    <br><span class="italic">Starring:</span> {$movieList[$key]->synop['stars']}</p>

    <iframe width="560" height="315" src="{$movieList[$key]->preview}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
    </iframe>
  </section>
OUTPUT;
}

function seatInput_module($type, $fillValue = [], $debug = false)   /// Seat Number inputs module - Booking
{
  global $seatText;
  $min = 1;
  $max = 10;
  $keySub = "";

  if ($type == "std-seats") {
    $keySub = "ST";
  } elseif ($type == "fc-seats") {
    $keySub = "FC";
  }

  echo "<div id='$type'>\n";

  // ** A4 ** Increment, Decrement buttons and Subtotal span elements added
  foreach ($seatText as $code => $text) {
    if (strpos($code, $keySub) !== false) {
      echo "\t<label for='$code'>$text</label>\n";
      echo "\t<div class='number-input'>\n";
      echo "\t\t<button type='button' class='inc-dec-button' onclick='decrInput(\"$code\")'>-</button>\n";
      if ($debug == false) {
        echo "\t\t<input type='number' id='$code' class='num-field' name='seats[$code]' min='$min' max='$max' title='$text Seats' value='{$fillValue[$code]}' onclick='updatePrice()' oninput='verifyInput(id)' />\n";
      } elseif ($debug == true) {
        echo "\t\t<input type='text' id='$code' class='num-field' name='seats[$code]' title='$text Seats' value='{$fillValue[$code]}' onclick='updatePrice()' />\n";
      }
      echo "\t\t<button type='button' class='inc-dec-button' onclick='incrInput(\"$code\")'>+</button>\n";
      echo "\t\t<span id='$code-sub' class='subtotal'></span>\n";
      echo "\t</div>\n";
    }
  }
  echo "</div>\n";
}

function sessionOptions_module()    /// Session Radio options module - Booking
{
  global $movieList;
  global $dayText;

  echo "\n<fieldset id='session'>\n";
  echo "\t<legend>Session</legend>\n";
  foreach ($movieList[$_GET["movie"]]->sessions as $day => $time) {
    $isChecked = setChecked($_POST['day'], $day);
    echo "\t<input type='radio' id='{$dayText[$day]}' name='day' value='$day' $isChecked onclick='isDiscounted(\"{$time}\")' required />\n";
    echo "\t<label for='{$dayText[$day]}' tabindex='0'>{$dayText[$day]} {$time}</label>\n";
  }
  echo "</fieldset>\n";
}

function appendFile()    /// Function to add booking data to spreadsheet file
{
  global $movieList;
  global $seat_prices;

  $discounted = $_SESSION["discounted"];
  $file = "bookings.txt";
  $now = date('d/m h:i');
  $seatData = [];
  $total = 0;

  foreach ($_SESSION["seats"] as $code => $num) {
    if (empty($num)) {
      $seatData[] = 0;
      $seatData[] = 0;
    } else {
      $seatData[] = $num;
      $seatData[] = $num * $seat_prices[$discounted][$code];
      $total += end($seatData);
    }
  }

  $gst = curr_format($total / 11);

  $cells = array_merge(
    [$now],
    $_SESSION["user"],
    [$_SESSION["movie"]],
    [$_SESSION["day"]],
    [$movieList[$_SESSION["movie"]]->sessions[$_SESSION["day"]]],
    $seatData,
    [$total],
    [$gst]
  );

  if (($fp = fopen($file, "a")) && (flock($fp, LOCK_EX))) {
    if (fputcsv($fp, $cells, "\t")) {
    }
    flock($fp, LOCK_UN);
    fclose($fp);
  }
}

function receiptTable_module()    /// Receipt Table module - Receipt
{
  global $seat_prices;
  global $seatText;

  $discounted = $_SESSION["discounted"];

  echo <<< "HEAD"
  <table>
      <tr>
          <th>Seat Type</th>
          <th>Number</th>
          <th>Price</th>
      </tr>
HEAD;
  $totalPrice = 0;
  foreach ($_SESSION["seats"] as $code => $num) {
    if (!empty($num)) {
      $subPrice = curr_format($num * $seat_prices[$discounted][$code]);
      $totalPrice += $subPrice;
      echo "\t<tr>\n";
      echo "\t\t<td>$code - {$seatText[$code]}</td>\n";
      echo "\t\t<td class='center'>$num</td>\n";
      echo "\t\t<td class='right'>\$$subPrice</td>\n";
      echo "\t</tr>\n";
    }
  }
  $gst = curr_format($totalPrice / 11);
  $totalPrice = curr_format($totalPrice);
  echo <<< "TAIL"
      <tr>
          <td class="blank"></td>
          <th class="right">GST</th>
          <td class="right">\$$gst</td>
      </tr>
      <tr>
          <td class="blank"></td>
          <th class="right">Total</th>
          <td class="right">\$$totalPrice</td>
      </tr>
  </table>\n
TAIL;
}

function ticket_module()    /// Ticket module - Receipt
{
  global $movieList;
  global $dayText;
  global $seatText;
  global $seat_prices;

  $discounted = $_SESSION["discounted"];
  $movieObj = $movieList[$_SESSION["movie"]];
  $receiptTitle = $movieObj->title;
  $receiptSession = $dayText[$_SESSION["day"]] . " " . $movieObj->sessions[$_SESSION["day"]];

  foreach ($_SESSION["seats"] as $code => $num) {
    if (!empty($num)) {
      $price = curr_format($seat_prices[$discounted][$code]);
      for ($i = 1; $i <= $num; $i++) {
        echo "\n<div id='outer'>\n";
        echo "\t<span id='title' class='details'>$receiptTitle</span>\n";
        echo "\t<img id='rating' class='details' src='{$movieObj->get_ratingURL()}' alt='{$movieObj->get_ratingAlt()}' />\n";
        echo "\t<span id='director' class='details'>Director: {$movieObj->synop['director']}</span>\n";
        echo "\t<span id='session' class='details'>$receiptSession</span>\n";
        echo "\t<span id='type' class='details'>{$seatText[$code]}<br>($i) \${$price}</span>\n";
        echo "\t<img id='back' src='./media/ticket.png' alt='ticket' />\n";
        echo "</div>\n";
      }
    }
  }
}
