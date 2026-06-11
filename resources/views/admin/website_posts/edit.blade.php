@extends('layouts.admin')

@section('title', 'Edit Konten Website - SIMAS')
@section('page_title', 'Edit Konten Website')

@section('content')
<div class="rounded-lg bg-white p-6 shadow">
    <form action="{{ route('website-posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.website_posts._form')

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('website-posts.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
