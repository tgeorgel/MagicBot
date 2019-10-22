<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

      <h1>Overview</h1>
      <hr></hr>
      <ul class="nav nav-tabs">
        <li><a href="#">Home</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="#">Messages</a></li>
        <li class="dropdown pull-right">
           <a href="#" data-toggle="dropdown" class="dropdown-toggle">Dropdown<strong class="caret"></strong></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>

    </div>
  </div>

  <div style="height: 1px;" class="margin-top-30"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


      <h3>Objectives</h3>
      <hr></hr>

      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <label for="donations">This month donations</label>
        <div name="donations" class="progress">
          <?php $scripts->printProgBar(0, 120, 52, false, true); ?>
        </div>
      </div>

    </div>
  </div>

</div>
