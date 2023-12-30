<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Impersonate</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="flex items-center justify-center h-screen bg-gray-50">
        <div class="text-center">

            <h1 class="text-4xl font-light mb-6 text-gray-800">Impersonate</h1>

            <div id="alert-border-4" class="mb-12 flex items-center p-4 mt-16 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                  In some cases, impersonate not work in the same window, and in the first attempt. If this happens, try again.
                </div>
            </div>

            <a 
                href="{{ route(config('laravel-auth.routes.as') . config('laravel-auth.routes.names.impersonate-token'), $token) }}?redirect=true" 
                class="bg-blue-500 hover:bg-blue-600 text-white mb-12 font-bold py-2 px-4 rounded transition duration-300 ease-in-out shadow-lg hover:shadow-xl"
                target="_blank">
                Check impersonate.
            </a>

            <div id="countdown" class="text-2xl font-bold text-red-600 mt-12"></div>

        </div>

    </body>
    <script>

        var countdownElement = document.getElementById('countdown');
        var targetTime = <?php echo $timestamp; ?>; // Tiempo objetivo en segundos
        var interval = setInterval(function() {
            var currentTime = Math.floor(Date.now() / 1000); // Tiempo actual en segundos
            var remainingTime = targetTime - currentTime;
            countdownElement.innerHTML = `Time remaining: ${remainingTime} seconds`;

            if (remainingTime <= 0) {
                clearInterval(interval);
                countdownElement.innerHTML = 'Time is up!';
            }
        }, 1000);

        if (window.location.search.includes('redirect=true')) {
            window.location.href = '{{ url('/') }}';
        }
    </script>
</html>
