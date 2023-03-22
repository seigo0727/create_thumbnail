@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>編集</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="form-content"></div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- ページごとCSSの指定
    <link rel="stylesheet" href="/css/xxx.css">
    --}}
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let formContent = document.querySelector('.form-content');
            formContent.innerHTML = `{!! $html !!}`;

        });
    </script>
@stop
