<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Application Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <!-- Your header content here -->
</header>

<main>
    @yield('content')
</main>

<footer>
    <!-- Your footer content here -->
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap components
        $('[data-toggle="modal"]').modal();
    });
</script>
</body>
</html>
