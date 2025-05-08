<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SparkPHP - A Minimal PHP Framework</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-zinc-950 text-white font-sans">

  <section class="h-screen flex flex-col items-center justify-center text-center px-6 py-16 space-y-10">
    
    <div class="flex items-center gap-3">
      <i data-feather="zap" class="text-yellow-400 w-8 h-8"></i>
      <h1 class="text-4xl md:text-6xl font-bold text-yellow-400">SparkPHP</h1>
    </div>

    <p class="text-xl md:text-2xl max-w-xl text-zinc-300">
      A minimal and lightweight PHP framework to spark your next project.
    </p>

    <div class="relative w-full max-w-xl flex items-center justify-between bg-zinc-900 gap-2 rounded px-3 py-2">
      <p class="text-yellow-400 overflow-hidden text-left text-sm md:text-base">
        git clone https://github.com/violentanirudh/sparkphp.git
      </p>
      <button onclick="copyCommand(this)" class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1 text-sm rounded shadow transition">
        Copy
      </button>
    </div>

    <div class="flex gap-4 flex-wrap justify-center">
      <a href="https://github.com/violentanirudh/sparkphp" target="_blank" class="bg-yellow-500 hover:bg-yellow-600 text-black px-6 py-3 rounded shadow transition">
        <i data-feather="github" class="inline w-5 h-5 mr-2"></i> View on GitHub
      </a>
      <a href="/docs" class="border border-zinc-800 px-6 py-3 rounded hover:bg-zinc-900 transition">
        <i data-feather="book" class="inline w-5 h-5 mr-2"></i> Read Docs
      </a>
    </div>

      <footer class="text-yellow-500 text-center py-6">
        Built with  <i data-feather="heart" class="inline w-5 h-5"></i> by Anirudh Singh.
      </footer>

  </section>


  <script>
    feather.replace();
    function copyCommand(element) {
      const text = "git clone https://github.com/violentanirudh/sparkphp.git";
      navigator.clipboard.writeText(text).then(() => {
        element.innerHTML = 'Copied';
        setTimeout(() => {
            element.innerHTML = 'Copy';
        }, 1000);
      });
    }
  </script>

</body>
</html>
