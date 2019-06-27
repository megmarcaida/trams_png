 <!-- Sidebar -->
    <ul class="sidebar navbar-nav toggled">
      <li class="nav-item active">
        <a class="nav-link" href="/TRAMS/public/">
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
          @if(Auth::user()->role_id != 3)  
          <a class="dropdown-item" href="/TRAMS/public/recordmaintenance/supplier">Supplier</a>
          <a class="dropdown-item" href="/TRAMS/public/recordmaintenance/truck">Trucks</a>
          @endif
          <a class="dropdown-item" href="/TRAMS/public/recordmaintenance/driver">Driver</a>
          <a class="dropdown-item" href="/TRAMS/public/recordmaintenance/assistant">Assistant</a>
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>

      @if(Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 4)  
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Master File</span>
        </a>
        <div class="dropdown-container">
          <!-- <h6 class="dropdown-header">Record Maintenance:</h6> -->
          <a class="dropdown-item" href="/TRAMS/public/masterfile/roles">Roles</a>
          <a class="dropdown-item" href="/TRAMS/public/masterfile/users">Users</a>
          <a class="dropdown-item" href="/TRAMS/public/masterfile/reasons">Reasons</a>
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>
      @endif
     
     @if(Auth::user()->role_id != 3)   
     <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Scheduling</span>
        </a>
        <div class="dropdown-container">
          
          <a class="dropdown-item" href="/TRAMS/public/scheduler/index">Scheduling</a>
          <a class="dropdown-item" href="/TRAMS/public/scheduler/slottingschedule">Slotting Scheduling</a>
          <a class="dropdown-item" href="/TRAMS/public/scheduler/dock">Dock</a>
        </div>
      </li>
      @endif

      @if(Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 4)  
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Others / Dashboards</span>
        </a>
        <div class="dropdown-container">
          
          <a class="dropdown-item" href="/TRAMS/public/dashboard/parking">Parking Dashboard</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/executive">Executive Module</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/dock">Dock</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/gate">Gate</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/manual">Manual Process</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/reader">QR Code</a>
          <a class="dropdown-item" href="/TRAMS/public/others/parking">Parking Module</a>
          <a class="dropdown-item" href="/TRAMS/public/others/bannedIssueReporting">Banned and Issue Reporting</a>
        </div>
      </li>
      @endif
      <!-- <li class="nav-item">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li> -->
    </ul>