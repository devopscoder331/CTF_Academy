<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Save the Environment</title>
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/styles.css'])
  @endif
</head>
<body>
  <header>
    <img src="https://static.vecteezy.com/system/resources/previews/030/805/464/large_2x/green-natural-grass-and-trees-in-the-forest-concept-green-energy-ecology-and-environmental-care-free-photo.jpg" class="header-image">
    <div class="header-text">
      <h1 style="text-shadow: 2px 2px 4px rgba(0,0,0,1)">Our Environment, Our Future</h1>
      <h3 style="text-shadow: 2px 2px 4px rgba(0,0,0,1); margin-top: 6px;">Please help us in our journey of preserving the Earth's beautiful environment!</h3>
    </div>
  </header>

  <section class="banner" style="margin-top: -4px">
    <p style="font-weight: bold">Join us in making a difference. Together, we can protect our planet!</p>
  </section>

  <section class="about-us" style="padding-top: 30px; background-color: #227FA5; padding-bottom: 40px">
    <h2>About Us</h2>
    <p style="font-weight: bold">We are a group of passionate individuals dedicated to environmental conservation. Our mission is to raise awareness and inspire action to protect nature and its ecosystems.</p>
    <div class="about-cards" style="padding: 0px 40px 0px 40px;">
      <div class="card">
        <h3>Our Mission</h3>
        <p>We aim to create a world where humans live in harmony with nature, reducing pollution and conserving wildlife.</p>
      </div>
      <div class="card">
        <h3>Our Values</h3>
        <p>We believe in sustainability, eco-conscious living, and taking collective responsibility for our planet's future.</p>
      </div>
      <div class="card">
        <h3>Get Involved</h3>
        <p>Become part of our cause by volunteering, donating, or spreading the message of environmental protection.</p>
      </div>
    </div>
  </section>

  <section class="section team" style="background-color: darkslategray">
    <h2>Join Our Mailing List</h2>
    <p style="font-weight: bold">Stay up to date with the latest announcements, and be informed of ways you can help us!</p>
     <form id="mailingListForm">
       <input type="text" id="email" name="email" placeholder="Email" style="width: 400px; height: 35px; font-size: 15px; border:none; text-indent: 8px;"><br>
       <input type="submit" value="Join!" style="font-size: 15px; width: 400px; margin-top: 10px; height: 30px; background-color: #2a7f62; border: none; color: white; font-weight: bold; cursor: pointer">
     </form>
     <div id="responseMessage" style="margin-top: 20px;"></div>
  </section>

  <!-- Footer section -->
  <footer style="background: black; margin-top: -20px">
    <p style="margin: 0; padding: 10px">Environment CTF &copy; 2025 | {{ucfirst(App::environment())}} v{{Config::get('app.version')}}</p>
  </footer>
  <script>
        document.getElementById('mailingListForm').addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevent the default form submission behavior

            const email = document.getElementById('email').value;
            const responseMessage = document.getElementById('responseMessage');

            try {
                const response = await fetch('/mailing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: "email=" + email,
                });

                if (response.ok) {
                    const data = await response.json();
                    responseMessage.textContent = data.message; // Display success message
                    responseMessage.style.color = 'greenyellow';
                } else {
                    const errorData = await response.json();
                    responseMessage.textContent = errorData.message || 'An error occurred.';
                    responseMessage.style.color = 'red';
                }
            } catch (error) {
                responseMessage.textContent = 'Failed to send the request.';
                responseMessage.style.color = 'red';
            }
        });
    </script>
</body>
</html>
