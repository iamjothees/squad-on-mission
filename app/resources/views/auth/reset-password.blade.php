<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Squad on Mission</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center bg-gray-50 dark:bg-gray-950 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-xl shadow border border-gray-200 dark:border-gray-800">
        <div>
            <h2 class="mt-2 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Reset Password</h2>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-3 rounded text-sm border border-red-200 dark:border-red-800/50">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="space-y-4 rounded-md shadow-sm">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="relative block w-full appearance-none rounded-t-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Email address" value="{{ old('email', $email) }}">
                </div>
                <div>
                    <label for="password" class="sr-only">New Password</label>
                    <input id="password" name="password" type="password" required class="relative block w-full appearance-none border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="New Password">
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="relative block w-full appearance-none rounded-b-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Confirm Password">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</body>
</html>
