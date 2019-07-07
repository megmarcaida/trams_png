 <!-- Sidebar -->
    <ul class="sidebar navbar-nav toggled">
      <li class="nav-item active">
        <a class="nav-link" href="/TRAMS/public/">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      @if(Auth::user()->role_id != 5)
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder-open"></i>
          <span>Record Maintenance</span>
        </a>
        <div class="dropdown-container">
          <!-- <h6 class="dropdown-header">Record Maintenance:</h6> -->  
          @if(Auth::user()->role_id != 3)  
          <a class="nav-link" href="/TRAMS/public/recordmaintenance/supplier">
            <i class="fas fa-fw fa-list"></i>
            <span>Supplier</span>
          </a>
          <a class="nav-link" href="/TRAMS/public/recordmaintenance/truck">
            <i class="fas fa-fw fa-truck"></i>
            <span>Trucks</span>
          </a>
          @endif
          <a class="nav-link" href="/TRAMS/public/recordmaintenance/driver">
            <i class="fas fa-fw fa-user"></i>
            <span>Driver</span>
          </a>
          <a class="nav-link" href="/TRAMS/public/recordmaintenance/assistant">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Assistant</span>
          </a>
          
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>
      @endif

      @if(Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 4 && Auth::user()->role_id != 5)  
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-cogs"></i>
          <span>Utilities</span>
        </a>
        <div class="dropdown-container">
          <!-- <h6 class="dropdown-header">Record Maintenance:</h6> -->
          <a class="nav-link" href="/TRAMS/public/masterfile/roles">
            <i class="fas fa-fw fa-user-tag"></i>
            <span>Roles</span>
          </a>
          <a class="nav-link" href="/TRAMS/public/masterfile/users">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Users</span>
          </a>
          <a class="nav-link" href="/TRAMS/public/masterfile/reasons">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Reasons</span>
          </a>
          <!-- <a class="dropdown-item" href="/TRAMS/public/masterfile/roles">Roles</a>
          <a class="dropdown-item" href="/TRAMS/public/masterfile/users">Users</a>
          <a class="dropdown-item" href="/TRAMS/public/masterfile/reasons">Reasons</a> -->
          <!-- <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Other Pages:</h6>
          <a class="dropdown-item" href="404.html">404 Page</a>
          <a class="dropdown-item" href="blank.html">Blank Page</a> -->
        </div>
      </li>
      @endif
     
     @if(Auth::user()->role_id != 3 && Auth::user()->role_id != 5)   
     <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-calendar-alt"></i>
          <span>Scheduling</span>
        </a>
        <div class="dropdown-container">
          <a class="nav-link" href="/TRAMS/public/scheduler/index">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Scheduler</span>
          </a>
          <a class="nav-link" href="/TRAMS/public/scheduler/dock">
            <i class="fas fa-fw fa-ship"></i>
            <span>Dock</span>
          </a>
          
          <!-- <a class="dropdown-item" href="/TRAMS/public/scheduler/index">Scheduling</a>
          <a class="dropdown-item" href="/TRAMS/public/scheduler/dock">Dock</a> -->
        </div>
      </li>
      @endif

      @if(Auth::user()->role_id != 3 && Auth::user()->role_id != 2 && Auth::user()->role_id != 4)  
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-suitcase"></i>
          <span>Others / Dashboards</span>
        </a>
        <div class="dropdown-container">
          
          <a class="nav-link" href="/TRAMS/public/dashboard/parking">
            <i class="fas fa-fw fa-parking"></i>
            <span>Parking Dashboard</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/executive">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Executive Module</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/dock">
            <i class="fas fa-fw fa-ship"></i>
            <span>Dock</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/gate">
            <i class="fas fa-fw fa-torii-gate"></i>
            <span>Gate</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/manual">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Manual Process</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/reader">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>QR Code</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/others/parking">
            <i class="fas fa-fw fa-parking"></i>
            <span>Parking Module</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/others/bannedIssueReporting">
            <i class="fas fa-fw fa-exclamation"></i>
            <span>Issue Reporting</span>
          </a>

          <!-- <a class="dropdown-item" href="/TRAMS/public/dashboard/parking">Parking Dashboard</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/executive">Executive Module</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/dock">Dock</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/gate">Gate</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/manual">Manual Process</a>
          <a class="dropdown-item" href="/TRAMS/public/dashboard/reader">QR Code</a>
          <a class="dropdown-item" href="/TRAMS/public/others/parking">Parking Module</a>
          <a class="dropdown-item" href="/TRAMS/public/others/bannedIssueReporting">Banned and Issue Reporting</a> -->
        </div>
      </li>
      @elseif(Auth::user()->role_id == 3 || Auth::user()->role_id == 2 || Auth::user()->role_id == 4)
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-btn" href="#">
          <i class="fas fa-fw fa-folder"></i>
          <span>Others / Dashboards</span>
        </a>
        <div class="dropdown-container">

          <a class="nav-link" href="/TRAMS/public/dashboard/manual">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Manual Process</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/dashboard/reader">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>QR Code</span>
          </a>

          <a class="nav-link" href="/TRAMS/public/others/parking">
            <i class="fas fa-fw fa-parking"></i>
            <span>Parking Module</span>
          </a>
        </div>
      </li>
      @endif
      <!-- <li class="nav-item">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li> -->
    </ul>