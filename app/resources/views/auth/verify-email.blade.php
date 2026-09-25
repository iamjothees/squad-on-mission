<!DOCTYPE html>
<html lang="en" class="h-full bg-surface">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Verify Email - Squad on Mission</title>
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
 <h2 class="mt-2 text-center text-3xl font-bold tracking-tight text-fg">Verify Your Email</h2>
 <p class="mt-4 text-center text-sm text-fg-muted">
 Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
 </p>
 </div>

 @if (session('status') == 'verification-link-sent')
 <div class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 p-3 rounded text-sm border border-green-200 dark:border-green-800/50 text-center">
 A new verification link has been sent to the email address you provided during registration.
 </div>
 @endif

 <div class="mt-8 flex items-center justify-between">
 <form method="POST" action="{{ route('verification.send') }}">
 @csrf
 <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-accent text-accent-fg py-2 px-4 text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
 Resend Verification Email
 </button>
 </form>

 <form method="POST" action="{{ route('logout') }}">
 @csrf
 <button type="submit" class="text-sm font-medium text-fg-muted hover:text-fg">
 Log Out
 </button>
 </form>
 </div>
 </div>
</body>
</html>
