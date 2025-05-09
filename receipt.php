<?php
include_once("tools.php");

// Redirects to the index page if the movie code or session day for that movie are incorrect 
// or no session set
if (!isset($_SESSION)) {
    header("Location: index.php");
} elseif ((!array_key_exists($_SESSION["movie"], $movieList)) ||
    (!array_key_exists($_SESSION["day"], $movieList[$_SESSION["movie"]]->sessions))
) {
    header("Location: index.php");
}

$movieObj = $movieList[$_SESSION["movie"]];
$receiptTitle = $movieObj->title;
$receiptSession = $dayText[$_SESSION["day"]] . " " . $movieObj->sessions[$_SESSION["day"]];

top_module("Booking Receipt");
?>

<body>
    <div id="main">
        <header>
            <!-- Original Creative Commons film strip SVG used to create logo attributed to:
    "Nevit Dilmen, CC BY-SA 3.0 <https://creativecommons.org/licenses/by-sa/3.0>, via Wikimedia Commons -->
            <div id="title">
                <img src='./media/LunardoLogo.png' alt='Lunardo Logo' />
                <h1>Lunardo</h1>
            </div>
            <div id="date">
                <?= date("j M Y") ?>
            </div>
            <button onclick="window.print()">Print Receipt</button>
            <span>Lunardo Cinema P/L</span>
            <span>123 Short St. Wangaratta Vic. 3677</span>
            <span id="contacts">
                <span>Email: <a href='mailto:contactus@lunardo.com.au'>contactus@lunardo.com.au</a></span>
                <span>Phone: <a href='tel:+61414100100'>0414 100 100</a></span>
            </span>
        </header>

        <section id="receipt">
            <h2>Booking Reciept</h2>
            <div id="user">
                <span>
                    <span class="label">Name</span>
                    <span class="data"><?= $_SESSION["user"]["name"] ?></span>
                </span>
                <span>
                    <span class="label">Email</span>
                    <span class="data"><?= $_SESSION["user"]["email"] ?></span>
                </span>
                <span>
                    <span class="label">Mobile</span>
                    <span class="data"><?= $_SESSION["user"]["mobile"] ?></span>
                </span>
            </div>
            <div id="booking">
                <span class="label">Booking</span>
                <span class="data"><?= $receiptTitle ?></span>
                <span class="data">@ <?= $receiptSession ?></span>
                <?php
                if ($_SESSION["discounted"] == "discounted") {
                    echo "<span class='data'>(Discounted session)</span>\n";
                }
                ?>
            </div>

            <div>
                <?= receiptTable_module() ?>
            </div>

            <!-- Links included for testing purposes
        <a href="booking.php?movie=FAM">
            <h4>Bookings</h4>
        </a>
        <a href="index.php">
            <h4>Home</h4>
        </a> -->

        </section>

        <section id="tickets">
            <h2>Tickets</h2>
            <?= ticket_module() ?>
        </section>
    </div>

    <?php
    debug_module();
    printMyCode();
    ?>

</body>

</html>