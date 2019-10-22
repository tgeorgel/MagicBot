<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle Menu</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="index.php" class="navbar-brand" style="height: 100%;">
      </a>
    </div>


    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="?p=home">Overview</a></li>
        <li><a href="?p=srv">Server Options</a></li>
        <li><a href="?p=bot">Bot Options</a></li>
      </ul>


      <ul class="nav navbar-nav navbar-right">
        <li><p class="navbar-text">Administrator</p></li>
        <li role="separator" class="divider"></li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo htmlentities($_SESSION['username']); ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="?p=settings">Settings</a></li>
            <li><a href="?p=accounts">Accounts</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="mysql/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>

    </div>
  </div>
</nav>
