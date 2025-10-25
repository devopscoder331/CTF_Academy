<!DOCTYPE html>
<html lang="en">


<x-head-component />

<body>

<x-header-nav :page="'dashboard'" :user="$user" />

        <div class="main">

             <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">Mailing List</h1>
                </div>

    <table>
        <thead>
            <tr>
                <th>Email Address</th>
                <th>Subscribed Date & Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailing as $mail)
            <tr>
                <td>{{$mail['email']}}</td>
                <td>{{$mail['created_at']}}</td>
                <td><h3 class="t-op-nextlvl label-tag color-{{$mail['status']}}" style="margin-left: auto; margin-right: auto;">
                @if($mail['status'] == '1')
                  Subscribed
                @else
                  Unsubscribed
                @endif
                </h3></td>
            </tr>
            @endforeach
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
