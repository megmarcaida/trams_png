 <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="/">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Record Maintenance</span>
        </a>
        <div class="dropdown-container">
          <!-- <h6 class="dropdown-header">Record Maintenance:</h6> -->
          @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 4)  
          <a class="dropdown-item" href="/recordmaintenance/supplier">Supplier</a>
          <a class="dropdown-item" href="/recordmaintenance/truck">Trucks</a>
          @endif
          <a class="dropdown-item" href="/recordmaintenance/driver">Driver</a>
          <a class="dropdown-item" href="/recordmaintenance/assistant">Assistant</a>
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Master File</span>
        </a>
        <div class="dropdown-container">
          <!-- <h6 class="dropdown-header">Record Maintenance:</h6> -->
          <a class="dropdown-item" href="/masterfile/roles">Roles</a>
          <a class="dropdown-item" href="/masterfile/users">Users</a>
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>
      
     <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Scheduling</span>
        </a>
        <div class="dropdown-container">
          
          <a class="dropdown-item" href="/scheduler/index">Scheduling</a>
          <a class="dropdown-item" href="/scheduler/slottingschedule">Slotting Scheduling</a>
          <a class="dropdown-item" href="/scheduler/dock">Dock</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Stand Alone App</span>
        </a>
        <div class="dropdown-container">
          
          <a class="dropdown-item" href="/dashboard/parking">Parking</a>
          <a class="dropdown-item" href="/dashboard/dock">Dock</a>
          <a class="dropdown-item" href="/dashboard/gate">Gate</a>
          <a class="dropdown-item" href="/dashboard/manual">Manual Process</a>
        </div>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li> -->
    </ul>