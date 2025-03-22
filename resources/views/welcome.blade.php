@extends('Layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hallo Apakabar</h3>
        <div class="card-tools">
            <a href="{{ route('logout') }}" class="btn btn-sm btn-danger" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin logout?')) { window.location.href = this.href; }">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <div class="card-body">
        Selamat Datang semua, ini adalah halaman utama dari aplikasi ini
    </div>
</div>
@section('scripts')
    @parent
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endsection
@endsection
