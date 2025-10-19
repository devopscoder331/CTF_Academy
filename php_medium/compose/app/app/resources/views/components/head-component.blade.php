<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport" 
          content="width=device-width, 
                   initial-scale=1.0">
    <title>Management Dashboard</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/admin.css'])
    @endif
    <style>
table {
            width: 100%;
            margin: 20px 0px 5px;
            border-collapse: collapse; /* Removes default spacing between table cells */
            table-layout: fixed; /* Ensures equal column widths */
        }

        th, td {
            text-align: center;
            padding: 10px;
            word-wrap: break-word; /* Ensures content wraps if it's too long */
        }

        th {
            font-weight: bold;
        }
        .link-properties {
            text-decoration: none;
            color: black;
        }
</style>
</head>
