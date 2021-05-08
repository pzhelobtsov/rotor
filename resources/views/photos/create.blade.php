@extends('layout')

@section('title', __('photos.create_photo'))

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/photos">{{ __('index.photos') }}</a></li>
            <li class="breadcrumb-item active">{{ __('photos.create_photo') }}</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="section-form mb-3 shadow">
        <form action="/photos/create" method="post" enctype="multipart/form-data">
            <input type="hidden" name="token" value="{{  $_SESSION['token'] }}">

            <div class="mb-3{{ hasError('title') }}">
                <label for="inputTitle" class="form-label">{{ __('photos.name') }}:</label>
                <input type="text" class="form-control" id="inputTitle" name="title" maxlength="50" value="{{ getInput('title') }}" required>
                <div class="invalid-feedback">{{ textError('title') }}</div>
            </div>

            <div class="mb-3{{ hasError('text') }}">
                <label for="text" class="form-label">{{ __('photos.description') }}:</label>
                <textarea class="form-control markItUp" id="text" rows="5" name="text">{{ getInput('text') }}</textarea>
                <div class="invalid-feedback">{{ textError('text') }}</div>
            </div>

            @include('app/_upload_image', ['files' => $files, 'type' => App\Models\Photo::$morphName])

            <div class="form-check">
                <input type="hidden" value="0" name="closed">
                <input type="checkbox" class="form-check-input" value="1" name="closed" id="closed"{{ getInput('closed') ? ' checked' : '' }}>
                <label class="form-check-label" for="closed">{{ __('main.close_comments') }}</label>
            </div>

            <button class="btn btn-success">{{ __('main.add') }}</button>
        </form>
    </div>
@stop
