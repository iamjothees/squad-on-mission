<!DOCTYPE html>
<html lang="en" class="h-full bg-surface">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Login - Squad on Mission</title>
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
 <h2 class="mt-2 text-center text-3xl font-bold tracking-tight text-fg">Sign in to your account</h2>
 <p class="mt-2 text-center text-sm text-fg-muted">
 If you were just invited, enter your username and leave the password blank to set up your account.
 </p>
 </div>
 <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
 @csrf
 
 @if($errors->any())
 <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-3 rounded text-sm border border-red-200 dark:border-red-800/50">
 {{ $errors->first() }}
 </div>
 @endif

 <div class="space-y-4 rounded-md shadow-sm">
 <div>
 <label for="username" class="sr-only">Email or Username</label>
 <input id="username" name="username" type="text" autocomplete="username" required class="relative block w-full appearance-none rounded-t-md border border-border bg-surface px-3 py-2 text-fg placeholder-gray-500 focus:z-10 focus:border-accent focus:outline-none focus:ring-accent sm:text-sm" placeholder="Email or Username" value="{{ old('username') }}">
 </div>
 <div>
 <label for="password" class="sr-only">Password</label>
 <input id="password" name="password" type="password" autocomplete="current-password" class="relative block w-full appearance-none rounded-b-md border border-border bg-surface px-3 py-2 text-fg placeholder-gray-500 focus:z-10 focus:border-accent focus:outline-none focus:ring-accent sm:text-sm" placeholder="Password">
 </div>
 </div>

 <div class="flex items-center justify-between">
 <div class="flex items-center">
 <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-border text-accent focus:ring-accent bg-surface dark:border-gray-600">
 <label for="remember" class="ml-2 block text-sm text-fg">Remember me</label>
 </div>
 <div class="text-sm">
 <a href="{{ route('password.request') }}" class="font-medium text-accent hover:text-accent hover:opacity-80">
 Forgot your password?
 </a>
 </div>
 </div>

 <div>
 <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-accent text-accent-fg py-2 px-4 text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
 Sign in
 </button>
 </div>
 </form>

 @if(app()->environment('local'))
 <div class="mt-8 border-t border-border pt-6">
 <p class="text-xs text-center text-fg-muted mb-4 font-bold uppercase tracking-wider">One-Click Dev Login</p>
 <div class="grid grid-cols-2 gap-3">
 @foreach(\App\Models\User::all() as $user)
 <form action="{{ route('login.post') }}" method="POST">
 @csrf
 <input type="hidden" name="username" value="{{ $user->username }}">
 <input type="hidden" name="password" value="{{ env('DEV_LOGIN_PASSWORD', 'password') }}">
 <button type="submit" class="w-full flex justify-center py-2 px-4 border border-border rounded-md shadow-sm bg-surface /50 text-sm font-medium text-fg-muted hover:bg-surface dark:hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-colors">
 Login as {{ $user->name }}
 </button>
 </form>
 @endforeach
 </div>
 </div>
 @endif
 </div>
</body>
</html>
