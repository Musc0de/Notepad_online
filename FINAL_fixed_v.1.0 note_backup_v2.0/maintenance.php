<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom animation styles */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen font-sans">
    <div class="container p-8 rounded-lg shadow-lg bg-white text-center">
        <div class="mb-6">
            <div class="w-16 h-16 border-t-4 border-blue-500 border-solid rounded-full border-opacity-50 spin mx-auto"></div>
        </div>
        <h1 class="text-3xl font-bold mb-2">Under Maintenance</h1>
        <p class="text-gray-600 mb-4">We're working hard to improve your experience. We'll be back soon!</p>
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div class="text-xs font-semibold inline-block py-1 px-2 rounded-full text-blue-600 bg-blue-200 uppercase last:mr-0 mr-1">
                    Loading...
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to animate the progress bar
        const progressFill = document.querySelector('.bg-blue-500');
        let width = 0;
        const interval = setInterval(() => {
            width += 1;
            progressFill.style.width = `${width}%`;
            if (width >= 100) {
                clearInterval(interval);
            }
        }, 100); // Adjust speed as needed
    </script>
</body>
</html>
