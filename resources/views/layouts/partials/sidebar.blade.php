<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Portal Kasbon</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="/dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#profileCollapse"
            aria-expanded="true" aria-controls="profileCollapse">
            <i class="fas fa-fw fa-user"></i>
            <span>Profil Saya</span>
        </a>
        <div id="profileCollapse" class="collapse" aria-labelledby="profileCollapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Setting Profil :</h6>
                <a class="collapse-item" href="{{ route('settings.profile.edit') }}">Profil Saya</a>
                <a class="collapse-item" href="{{ route('settings.profile.change-password') }}">Ganti Passowrd</a>
            </div>
        </div>
    </li>
    @if (auth()->user()->role->name === 'super_admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.roles.index') }}">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Manajemen Role</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.kasbons.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Manajemen Kasbon</span>
        </a>
    </li>
    <!-- Tambahkan menu lain sesuai kebutuhan -->

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
