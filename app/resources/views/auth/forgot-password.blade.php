<!DOCTYPE html>
<html lang="en" class="h-full bg-surface">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Forgot Password - Squad on Mission</title>
 @vite(['resources/css/app.css'])
 <script>
 (function () {
 var preference = localStorage.getItem('admin-theme') ?? 'system';
 var dark = preference === 'dark' || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
 document.documentElement.classList.toggle('dark', dark);
 })();
 </script>
</head>
<body class="h-full flex items-center justify-center bg-bg px-4 sm:px-6 lg:px-8">
 <div class="max-w-md w-full space-y-8 bg-surface p-8 rounded-xl shadow border border-border">
 <div>
 <h2 class="mt-2 text-center text-3xl font-bold tracking-tight text-fg">Forgot Password</h2>
 <p class="mt-2 text-center text-sm text-fg-muted">
 Enter your email address and we will send you a password reset link.
 </p>
 </div>
 
 @if (session('status'))
 <div class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 p-3 rounded text-sm border border-green-200 dark:border-green-800/50 text-center">
 {{ session('status') }}
 </div>
 @endif

 <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
 @csrf
 
 @if($errors->any())
 <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-3 rounded text-sm border border-red-200 dark:border-red-800/50">
 {{ $errors->first() }}
 </div>
 @endif

 <div class="space-y-4 rounded-md shadow-sm">
 <div>
 <label for="email" class="sr-only">Email address</label>
 <input id="email" name="email" type="email" autocomplete="email" required class="relative block w-full appearance-none rounded-md border border-border bg-surface px-3 py-2 text-fg placeholder-gray-500 focus:z-10 focus:border-accent focus:outline-none focus:ring-accent sm:text-sm" placeholder="Email address" value="{{ old('email') }}">
 </div>
 </div>

 <div>
 <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-accent text-accent-fg py-2 px-4 text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
 Email Password Reset Link
 </button>
 </div>
 
 <div class="text-center">
 <a href="{{ route('login') }}" class="text-sm font-medium text-accent hover:text-accent hover:opacity-80">
 Back to login
 </a>
 </div>
 </form>
 </div>
</body>
</html>
