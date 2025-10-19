<header>

        <div class="logosec">
            <div class="logo">Environment CTF</div>
        </div>
    <div class="message">
            <div class="circle">Hi, {{ $user->name }}</div>
            <div class="dp">
              <img src=/storage/files/{{$user->profile_picture}}
                    class="dpicn" 
                    alt="dp">
              </div>
        </div>


    </header>

    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <a href="/management/dashboard" class="link-properties">
                    <div class="nav-option" id="dashboard-link">
                        <img src=
"https://cdn2.iconfinder.com/data/icons/web-development-152/32/WebDevelopment-BasicOutline-30-512.png"
                            class="nav-img" 
                            alt="dashboard">
                        <h3>Dashboard</h3>
                    </div>
                    </a>

                    <a href="/management/profile" class="link-properties">
                    <div class="nav-option" id="profile-link">
                        <img src=
"https://static.vecteezy.com/system/resources/previews/020/911/746/non_2x/user-profile-icon-profile-avatar-user-icon-male-icon-face-icon-profile-icon-free-png.png"
                            class="nav-img" 
                            alt="profile">
                        <h3>Profile</h3>
                    </div>
                    </a>

                    @if (App::environment() !== 'production')
                    <a href="/management/info" class="link-properties">
                    <div class="nav-option" id="info-link">
                        <img src=
"https://upload.wikimedia.org/wikipedia/commons/thumb/4/43/Minimalist_info_Icon.png/768px-Minimalist_info_Icon.png"
                            class="nav-img" 
                            alt="info">  
                        <h3>PHP Info</h3>  
                    </div>
                    </a>
                    @endif

                    <a href="/logout" class="link-properties">
                    <div class="nav-option logout">
                        <img src=
"https://cdn-icons-png.flaticon.com/512/126/126467.png"
                            class="nav-img" 
                            alt="logout">
                        <h3>Logout</h3>
                    </div>
                    </a>

                </div>
            </nav>
        </div>

<script>
  var pagepage = @json($page) + "-link";
  var pagepagematch = document.getElementById(pagepage);
  if (pagepagematch) {
    pagepagematch.classList.add('option1');
  }
</script>
