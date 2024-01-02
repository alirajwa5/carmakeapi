<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Models and Car Makes API Save</title>
    <!-- Bootstrap CSS link (you may need to adjust the version based on your setup) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Center the button */
        .centered {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h1 class="mt-5 mb-4">Car Models and Car Makes API Save</h1>

    <div class="centered">
        <div>
            <a href="{{ route('save.car.makes') }}" class="btn btn-primary btn-lg mb-3">Save Car Makes</a>
            <br>
            <a href="{{ route('save.car.models') }}" class="btn btn-success btn-lg">Save Car Models</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js (you may need to adjust the version based on your setup) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
