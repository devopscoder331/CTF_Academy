<!DOCTYPE html>
<html lang="en">


<x-head-component />

<body>

<x-header-nav :page="'info'" :user="$user" />

        <div class="main">

             <div style="background-color: black;" class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">PHP Info</h1>
                </div>
                   <?php phpinfo(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
