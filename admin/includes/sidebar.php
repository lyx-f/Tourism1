<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<div class="sidebar">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    <div class="sidenav-header">
      <a class="navbar-brand m-0" href="index.php">
        <h4>Hi Admin <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Unknown'; ?>!</h4>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">


      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link  active" href="index.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-tachometer-alt text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">MAP/NAVIGATION MANAGEMENT</h6>
        <li class="nav-item">
          <a class="nav-link  " href="map-nav.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-map text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1"> Map</span>
          </a>
        </li>

        <!-- <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ANALYTICS & REPORTS</h6>
        <li class="nav-item">
          <a class="nav-link  " href="analytics.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-chart-line text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Reports</span>
          </a>
        </li> -->




        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">VERIFICATION</h6>
        <li class="nav-item">
          <a class="nav-link  " href="verification.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-code text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Business Verification</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link  " href="generate_admin_code.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-code text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Admin Code</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">SUPPORT</h6>
        <li class="nav-item">
          <a class="nav-link" href="chat-support.php">
            <div
              class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-comments text-dark text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Contact Us</span>
          </a>
        </li>

      </ul>
    </div>
    <div class="sidenav-footer mx-3 ">

      <a class="btn bg-gradient-primary mt-3 w-100" href="logout.php">LOGOUT</a>
    </div>
  </aside>