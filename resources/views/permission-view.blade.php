<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body style="background-color: #222; display:flex; height:100vh;">
    <h1 style="margin:auto; color:#f5f5f5">Give Geolocation Access Permission</h1>
</body>
<script>
    function getLocationAndSendToLaravel() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(sendPositionToLaravel);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }


    // Send coordinates to Laravel route using a form submission
    function sendPositionToLaravel(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Create a form element
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '{{ route('getPrayerTimes') }}');

        // CSRF token is needed for POST requests
        var csrfField = document.createElement('input');
        csrfField.setAttribute('type', 'hidden');
        csrfField.setAttribute('name', '_token');
        csrfField.setAttribute('value', '{{ csrf_token() }}');
        form.appendChild(csrfField);

        // Add latitude and longitude as form fields
        var latField = document.createElement('input');
        latField.setAttribute('type', 'hidden');
        latField.setAttribute('name', 'latitude');
        latField.setAttribute('value', latitude);
        form.appendChild(latField);

        var longField = document.createElement('input');
        longField.setAttribute('type', 'hidden');
        longField.setAttribute('name', 'longitude');
        longField.setAttribute('value', longitude);
        form.appendChild(longField);

        // Append the form to the body and submit
        document.body.appendChild(form);
        form.submit();
    }

    // Call the getLocationAndSendToLaravel function to start retrieving coordinates
    getLocationAndSendToLaravel();
</script>

</html>