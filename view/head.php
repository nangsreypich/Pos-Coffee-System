<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Third Coffee Pos System</title>
    <link rel="icon" type="image/x-icon" href="../image/Logo_ASSIGMENT.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            "paging": true,
            "searching": true
        });
    });
    $(document).ready(function() {
        $('#drinkTable').DataTable({
            "paging": true, // Enable pagination
            "lengthMenu": [10, 25, 50, 100], // Set length menu options
            "searching": true // Enable searching
            // Add more options as needed
        });
    });
    $(document).ready(function() {
        $('#positionTable').DataTable({
            "paging": true, // Enable pagination
            "lengthMenu": [10, 25, 50, 100], // Set length menu options
            "searching": true // Enable searching
            // Add more options as needed
        });
    });
    $(document).ready(function() {
        $('#example1').DataTable({
            "paging": true, // Enable pagination
            "lengthMenu": [10, 25, 50, 100], // Set length menu options
            "searching": true // Enable searching
            // Add more options as needed
        });
    });
</script>
</head>
<style>
   .container-fluid{
        font-family: "Inter", sans-serif;
    }
</style>


