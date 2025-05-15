
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/index.css" />
    <title>Web Design Mastery | SoulTravel</title>
  </head>
  <body>
    <nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="#">e<span>CARGA</span>.</a>
        </div>
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
        <li><a href="#">About Us</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Contact Us</a></li>

        
      </ul>
      <div class="nav__btns">
      
        <button class="btn sign__up" onclick="location.href='login.php'">Register</button>

        <button class="btn sign__in" onclick="location.href='login.php'">Login</button>
      </div>
    </nav>
    <header class="header__container">
     
        <img src="https://content.presspage.com/uploads/685/c1920_femaleusingmobiledevice-774181.jpg?45004" alt="header" />
      </div>
      <div class="header__content">
                <h1>TAP<br />BOOK <span>Go! </span>  ITS That Easy</h1>
        <p>
          Booking made Easy, Riding made Safe
        </p>
        <form action="/">
          <div class="input__row">
            <div class="input__group">
              <h5>Destination</h5>
              <div>
                <span><i class="ri-map-pin-line"></i></span>
                <input type="text" placeholder="Paris, France" />
              </div>
            </div>
            <div class="input__group">
              <h5>Date</h5>
              <div>
                <span><i class="ri-calendar-2-line"></i></span>
                <input type="text" placeholder="17 July 2024" />
              </div>
            </div>
          </div>
          <button type="submit">Search</button>
        </form>
       
      </div>
    </header>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="assets/index.js"></script>
  </body>
</html>
