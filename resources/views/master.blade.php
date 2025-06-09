<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Landing Page - Tailwind</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    *{
        font-family: "Inter", sans-serif;
        font-style: normal;
    }
    .slider-container {
      overflow: hidden;
      position: relative;
    }
    .slider-track {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }
    .slider-item {
      flex: 0 0 100%;
    }
    .scroll-hidden::-webkit-scrollbar {
        display: none;
    }
    .scroll-hidden {
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
    }
  </style>
</head>
<body class="bg-white text-gray-800">
    <div class=" max-w-md mx-auto pt-4 pb-2 min-h-screen border">
        @yield('main')
    </div>

  <script>
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
</body>
</html>
