@extends('adminlte::page')

@section('title', 'laravel8 | メディア')

@section('content_header')
    <h1>メディア</h1>
@stop

@section('content')
    @if (session('messageType'))
        @if(session('messageType') == 'success')
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                {{ session('message') }}
            </div>
        @endif
    @endif
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('media.upload') }}" enctype="multipart/form-data">
                @method('POST')
                @csrf
                <div class="file-area text-center" style="margin-bottom: 20px; padding: 20px 0; color: #696969; border: dotted 4px #696969; background: #f0f0f0">
                    ファイルを選択<br>（クリック）
                </div>
                <input type="file" name="file" style="display: none;">
                <div class="form-group row alias-group">
                    <label class="col-md-2 col-form-label" for="alias-input">エイリアス</label>
                    <div class="col-md-10">
                        <input type="text" name="alias" id="alias-input" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">送信</button>
            </form>
            @if(!$items->isEmpty())
                <hr>
                <form class="delete-form" method="post">
                    @method("DELETE")
                    @csrf
                    <div class="card">
                        <div class="card-body row">
                            @foreach($items as $item)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header text-right">
                                            <button class="btn btn-danger btn-sm" type="button" onclick="onDelete('{{ $item->id }}')">×</button>
                                        </div>
                                        <img class="card-img-top" src="{{ asset('storage/' . $item->thumbnail_path) }}">
                                        <div class="card-body">
                                            <p>{{ $item->title }}</p>
                                            @if((string)$item->alias !== '')
                                                <p>エイリアス：<a href="{{ route('media.show', ['alias' => $item->alias]) }}" target="_blank">{{ $item->alias }}</a></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            @endif
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
        console.log('ページごとJSの記述');

        document.addEventListener('DOMContentLoaded', function() {
            var fileArea = document.querySelector('.file-area');
            var fileInput = document.querySelector('input[type="file"]');
            var aliasGroup = document.querySelector('.alias-group');
            var reader = new FileReader();
            fileArea.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                fileArea.style.display = "none";

                console.log(fileInput.value);
                reader.onload = function(event) {
                }
            });
        });

        var endpoint = `<?php echo $endpoint; ?>`;


        function onDelete(id)
        {
            var deleteForm = document.querySelector('.delete-form');
            if(confirm('削除しますか？')) {
                deleteForm.setAttribute('action', endpoint + id);
                deleteForm.submit();
            }
        }
    </script>
@stop
