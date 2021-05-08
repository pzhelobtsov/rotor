@extends('layout')

@section('title', __('news.create_title'))

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/admin">{{ __('index.panel') }}</a></li>
            <li class="breadcrumb-item"><a href="/admin/news">{{ __('index.news') }}</a></li>
            <li class="breadcrumb-item active">{{ __('news.create_title') }}</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="section-form mb-3 shadow cut">
        <form action="/admin/news/create" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3{{ hasError('title') }}">
                <label for="title" class="form-label">{{ __('main.title') }}:</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100" value="{{ getInput('title') }}" placeholder="{{ __('main.title') }}" required>
                <div class="invalid-feedback">{{ textError('title') }}</div>
            </div>

            <div class="mb-3{{ hasError('text') }}">
                <label for="text" class="form-label">{{ __('main.text') }}:</label>
                <textarea class="form-control markItUp" maxlength="10000" id="text" rows="10" name="text" placeholder="{{ __('main.text') }}" required>{{ getInput('text') }}</textarea>
                <div class="invalid-feedback">{{ textError('text') }}</div>
                <span class="js-textarea-counter"></span>
            </div>

            <div class="custom-file{{ hasError('image') }}">
                <label for="image" class="btn btn-sm btn-secondary form-label">
                    <input id="image" type="file" name="image" onchange="$('#upload-file-info').html(this.files[0].name);" hidden>
                    {{ __('main.attach_image') }}&hellip;
                </label>
                <span class="badge bg-info" id="upload-file-info"></span>
                <div class="invalid-feedback">{{ textError('image') }}</div>
            </div>

            <div class="form-check">
                <input type="hidden" value="0" name="closed">
                <input type="checkbox" class="form-check-input" value="1" name="closed" id="closed"{{ getInput('closed') ? ' checked' : '' }}>
                <label for="closed" class="form-check-label">{{ __('main.close_comments') }}</label>
            </div>

            <div class="form-check">
                <input type="hidden" value="0" name="top">
                <input type="checkbox" class="form-check-input" value="1" name="top" id="top"{{ getInput('top') ? ' checked' : '' }}>
                <label for="top" class="form-check-label">{{ __('news.show_on_the_homepage') }}</label>
            </div>

            <button class="btn btn-primary">{{ __('main.create') }}</button>
        </form>
    </div>
@stop
