<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketing Management Portal</title>
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/login.css'])
  @endif
</head>
<body>
<div class="container">
      <div class="forms-container">
        <div class="form-control signin-form">
          <form action="/login" method="POST" id="loginForm">
            <h2 style="padding-bottom: 20px;">Marketing Management Portal</h2>
            @csrf
            <input type="email" placeholder="Email" name="email" id="email" required />
            <input type="password" placeholder="Password" name="password" id="password" required />
            <label style ="padding-top: 10px;">
              <input type="checkbox" id="rememberCheckbox"/> Remember Me?
            </label>
            <input type="hidden" name="remember" id="remember" value="False">
            <button>Sign In</button>
            <p id="responseMessage" style="color: red; padding-top: 20px;"></p>
          </form>
        </div>
      </div>
      <div class="intros-container">
        <div class="intro-control signin-intro">
          <div class="intro-control__inner">
            <h2>Welcome back!</h2>
            <p>
              We are so happy to have you here. It's great to see you again. We hope you had a safe and enjoyable time away.
            </p>
          </div>
        </div>
      </div>
    </div>
    <script>
        const queryString = window.location.search;
        if(queryString) {
          const urlParams = new URLSearchParams(queryString);
          const error = urlParams.get('error')
          if(error) {
            document.getElementById('responseMessage').textContent = error;
          }
        }
    </script>
    <script>
      document.getElementById('loginForm').addEventListener('submit', function() {
        const checkbox = document.getElementById('rememberCheckbox');
        const hiddenInput = document.getElementById('remember');
        hiddenInput.value = checkbox.checked ? 'True' : 'False';
     });
    </script>
</body>
</html>
