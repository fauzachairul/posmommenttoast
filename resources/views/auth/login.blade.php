<div>
    <!-- I have not failed. I've just found 10,000 ways that won't work. - Thomas Edison -->
</div>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/feather-icons"></script>
    @vite('resources/css/app.css')
    <title>Login Page</title>
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-center mb-6">Momment Toast</h2>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul id="toastVal"
                    class="list-none px-5 w-full py-4 bg-red-200 rounded flex justify-between items-center">
                    @foreach ($errors->all() as $error)
                        <li class="font-medium">{{ $error }}</li>
                        <button id="btn-close" class="bg-red-300 p-2 rounded active:bg-red-400"><i
                                data-feather="x-square" class="text-gray-100"></i></button>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">

            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <div class="mb-4 flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:underline" href="#">
                        Forgot your password?
                    </a>
                @endif
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-slate-900 text-white py-2 px-4 rounded hover:bg-slate-700 transition duration-200 cursor-pointer">
                    Login
                </button>
            </div>
        </form>
    </div>

    @vite('resources/js/app.js')
    <script>
        feather.replace();

        const btnClose = document.getElementById('btn-close');



        btnClose.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.style.display = 'none';
        });
    </script>
</body>

</html>
