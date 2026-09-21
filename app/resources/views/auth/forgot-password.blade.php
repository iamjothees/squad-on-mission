<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Squad on Mission</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center bg-gray-50 dark:bg-gray-950 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-xl shadow border border-gray-200 dark:border-gray-800">
        <div>
            <h2 class="mt-2 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Forgot Password</h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
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
                    <input id="email" name="email" type="email" autocomplete="email" required class="relative block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Email address" value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Email Password Reset Link
                </button>
            </div>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Back to login
                </a>
            </div>
        </form>
    </div>
</body>
</html>
