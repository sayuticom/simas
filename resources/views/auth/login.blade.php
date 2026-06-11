@extends('layouts.admin')

@section('content')
    <div class="max-w-md mx-auto mt-16 bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">Masuk ke SIMAS</h1>

        @if($errors->any())
            <div class="mb-4 text-red-600">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required class="w-full mt-1 p-2 border rounded" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input name="password" type="password" required class="w-full mt-1 p-2 border rounded" />
            </div>
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center"><input type="checkbox" name="remember" class="mr-2"> Ingat saya</label>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded">Masuk</button>
            </div>
        </form>
    </div>
@endsection
