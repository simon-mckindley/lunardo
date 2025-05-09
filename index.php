<?php
include_once("tools.php");
top_module("Lunardo Cinema");
nav_module("index");
?>

<main>
  <section id='about-us'>

    <h2>About Us</h2>
    <p id="part1">Lunardo is your local cinema. We have been a proud part of the Wangaratta community for over fifty years.
      Local residents have enjoyed the movie going experience at Lunardo for generations, and we are excited to continue
      that experience for current and future generations.<br>
      To achieve this we have undertaken extensive renovations on the exterior, foyer and cinema seating, as well as upgrading the
      projection and sound technology. We are excited to announce the completion of these renovations which will upgrade your
      cinema experience.
    </p>

    <!-- Image sourced from http://photos.cinematreasures.org/production/photos/174084/1469209363/large.jpg?1469209363 -->
    <figure id="old-cinema">
      <img src="./media/old-cinema.jpg" alt="Black & white photo of the original exterior of Lunardo Cinema" />
      <figcaption>Lunardo Cinema circa 1966</figcaption>
    </figure>

    <p id="part2">
      Our cinema now offers amazing
      <a href='https://www.dolby.com/movies-tv/dolby-cinema/' target="_blank">
        <img id="dolby-logo" src="./media/dolby-logo.png" alt="Dolby Logo">3D Dolby Vision projection and Dolby Atmos sound</a>
      which is the latest cinema technology and will give you the best screen and sound experience possible.<br>
      We have also upgraded the seating and now offer two new seat types. The super comfortable Standard seats are great for
      any movie goer, and the luxurious, reclinable First Class seats will add another level of comfort to your
      cinema experience.<br><br>
      <em>Come and enjoy our new top-class cinema experience.</em>
    </p>

    <!-- Image sourced from https://d279m997dpfwgl.cloudfront.net/wp/2020/12/GettyImages-1150049038.jpg -->
    <figure id="new-cinema">
      <img src="./media/new-cinema.jpg" alt="Empty interior of renovated Lunardo Cinema showing the seats and screen" />
      <figcaption>Newly renovated theatre</figcaption>
    </figure>

  </section>

  <section id='seats-prices'>
    <h2>Seats and Prices</h2>
    <div id="seats-grid">
      <figure id="fc-fig">
        <figcaption>Reclinable First Class Seats</figcaption>
        <img src='./media/Profern-Verona-Twin.png' alt='Standard Cinema Seat' />
      </figure>

      <div id="fc-table" class="table">

        <?= table_module("First Class") ?>

      </div>

      <figure id="std-fig">
        <figcaption>Standard Seats</figcaption>
        <img src='./media/Profern-Standard-Twin.png' alt='First Class Cinema Seat' />
      </figure>

      <div id="std-table" class="table">

        <?= table_module("Standard") ?>

      </div>

    </div>
    <p id="disclaimer">*Discounted prices are only available on all Monday sessions, or the 12pm session on other weekdays.</p>
  </section>

  <section id='now-showing'>
    <h2>Now Showing</h2>
    <div id="card-grid">
      <!-- All movie poster images and movie summaries sourced from https://www.imdb.com/ a-->

      <?= card_module() ?>

    </div>
  </section>

</main>

<?php
end_module();
debug_module();
printMyCode();
?>

</body>

</html>