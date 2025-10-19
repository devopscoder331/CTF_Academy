<!DOCTYPE html>
<html lang="en">


<x-head-component />

<body>

<x-header-nav :page="'profile'" :user="$user" />

<style>
.profile-content {
  text-align: center;
  position: relative;
  padding: 20px 20px 0px 20px;
}
.not-bold {
  font-weight: normal;
}
input[type="file"] {
  display: none;
}
.custom-file-upload {
  border: 3px solid black;
  display: inline-block;
  padding: 5px 12px;
  cursor: pointer;
  background-color: white;
  font-size: 16px;
  top: 1px;
  position: relative;
  border-radius: 5px;
}
.custom-file-upload:hover {
  background-color: lightgray;
}
button {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 150px;
    }
button:hover {
    background-color: blue;
}
</style>

        <div class="main">

             <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">Profile</h1>
                </div>
                   <div class="profile-content">
                    <h2><span class="not-bold">Name:</span> {{ $user->name }} </h2>
                    <h2><span class="not-bold">Email:</span> {{ $user->email }} </h2>
                    <br/>
                    <h2 style="margin-bottom:5px;"><span class="not-bold">Profile Picture</span><h2>
                    <img src=/storage/files/{{ $user->profile_picture }} width=128 style="margin-bottom:20px;"/>
                    <form enctype="multipart/form-data" id="profileUpload">
                      @csrf
                      <label for="profile-pic" class="custom-file-upload">Choose New Picture</label>
                      <input type="file" id="profile-pic" name="upload" required>
                      <button type="submit">Upload</button>
                      <p id="selected-file" style="margin-top: 10px; font-size: 18px; font-weight: normal; padding:10px;"></p>
                      <p style="margin-top: 5px; font-size: 14px; font-weight: normal; padding:5px; color: red;" id="file-error"></p>
                    </form>
                   <script>
                     document.getElementById('selected-file').display = 'none';
                     document.getElementById('profile-pic').onchange = function (e) {
                       var uploadedname = e.target.files[0].name;
                       document.getElementById('selected-file').display = 'block';
                       document.getElementById('selected-file').textContent = "Selected File: " + e.target.files[0].name;
                     };
                     document.getElementById('profileUpload').addEventListener('submit', function (event) {
                       event.preventDefault();
                       let formData = new FormData(this);
                       fetch('/upload', {
                         method: 'POST',
                         body: formData
                       })
                       .then(response => response.json())  // Assuming the response is JSON
                       .then(data => {
                         if(data.error) {
                           document.getElementById("file-error").textContent = data.error.message;
                         }
                         else {
                           window.location.reload();
                         }
                       })
                       .catch(error => {
                         console.error('Error:', error);
                       });
                     });
                   </script>
                   </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
