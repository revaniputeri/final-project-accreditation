@extends('layouts.app')

@section('title', 'Dokumen Akhir')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Dokumen Akhir</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    @if(isset($error))
        <div class="alert alert-danger text-center mt-4">{{ $error }}</div>
    @elseif(isset($pdfUrl))
        <iframe src="{{ $pdfUrl }}" style="width: 100%; height: 90vh; border: none;"></iframe>
    @else
        <div class="alert alert-warning text-center mt-4">Tidak ada dokumen untuk ditampilkan.</div>
    @endif
</div>
@endsection
