@extends('layouts.app')

@section('title', 'Tambah Kontak')
@section('heading', 'Tambah Kontak')

@section('content')
<div class="content">
    <div class="scroll-area p-3 p-md-4">
        <div class="card shadow-sm mx-auto" style="max-width: 640px;">
            <div class="card-body">
                <form action="{{ route('contacts.store') }}" method="POST">
                    @include('contacts._form')
                </form>
            </div>
        </div>
    </div>
</div>

@include('contacts._kategori_modal')
@endsection
