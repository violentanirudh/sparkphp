<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SparkPHP - A Minimal PHP Framework</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white font-sans">

  <section class="min-h-screen flex flex-col items-center justify-center text-center px-6">
    <h1 class="text-4xl md:text-6xl font-bold mb-4 text-yellow-400">SparkPHP</h1>
    <p class="text-xl md:text-2xl max-w-xl mb-6 text-zinc-300">
      A minimal and lightweight PHP framework to spark your next project.
    </p>

    <pre class="bg-zinc-900 text-left text-green-400 px-4 py-2 rounded mb-6 shadow-md">
        composer create-project sparkphp/sparkphp my-app
    </pre>

    <div class="flex gap-4">
      <a href="https://github.com/your-username/sparkphp" target="_blank" class="bg-yellow-500 hover:bg-yellow-600 text-black px-6 py-3 rounded shadow transition">
        View on GitHub
      </a>
      <a href="/docs" class="border border-zinc-400 px-6 py-3 rounded hover:bg-zinc-800 transition">
        Read Docs
      </a>
    </div>
  </section>

  <footer class="text-sm text-zinc-500 text-center py-4">
    &copy; <?= date('Y') ?> SparkPHP. Built with ❤️ for beginners.
  </footer>

</body>
</html>
