@extends('layout')

@section('title', $photo->title)

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/photos">{{ __('index.photos') }}</a></li>
            <li class="breadcrumb-item"><a href="/photos/albums/{{ $photo->user->login }}">{{ __('photos.album') }}</a></li>
            <li class="breadcrumb-item active">{{ $photo->title }}</li>
        </ol>
    </nav>
@stop

@section('header')
    @if (isAdmin())
        <div class="dropdown float-right">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-wrench"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/admin/photos/edit/{{ $photo->id }}">{{ __('main.edit') }}</a>
                <a class="dropdown-item" href="/admin/photos/delete/{{ $photo->id }}?token={{ $_SESSION['token'] }}" onclick="return confirm('{{ __('photos.confirm_delete_photo') }}')">{{ __('main.delete') }}</a>
            </div>
        </div>
    @endif


    @if (getUser() && getUser('id') === $photo->user->id && ! isAdmin())
        <div class="dropdown float-right">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-wrench"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/photos/edit/{{ $photo->id }}">{{ __('main.edit') }}</a>
                <a class="dropdown-item" href="/photos/delete/{{ $photo->id }}?token={{ $_SESSION['token'] }}" onclick="return confirm('{{ __('photos.confirm_delete_photo') }}')">{{ __('main.delete') }}</a>
            </div>
        </div>
    @endif

    <h1>{{ $photo->title }}</h1>
@stop

@section('content')
    <div class="section mb-3 shadow">
        @foreach ($photo->files as $file)
            <div class="mb-3">
                <a href="{{ $file->hash }}" class="gallery" data-group="{{ $photo->id }}"><img class="img-fluid" src="{{ $file->hash }}" alt="image"></a>
            </div>
        @endforeach

        <div class="section-content">
            @if ($photo->text)
                {!! bbCode($photo->text) !!}<br>
            @endif

            <div class="my-2 js-rating">{{ __('main.rating') }}:
                @if (getUser() && getUser('id') !== $photo->user_id)
                    <a class="post-rating-down<?= $photo->vote === '-' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $photo->id }}" data-type="{{ $photo->getMorphClass() }}" data-vote="-" data-token="{{ $_SESSION['token'] }}"><i class="fa fa-thumbs-down"></i></a>
                @endif
                <b>{!! formatNum($photo->rating) !!}</b>
                @if (getUser() && getUser('id') !== $photo->user_id)
                    <a class="post-rating-up<?= $photo->vote === '+' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $photo->id }}" data-type="{{ $photo->getMorphClass() }}" data-vote="+" data-token="{{ $_SESSION['token'] }}"><i class="fa fa-thumbs-up"></i></a>
                @endif
            </div>

            {{ __('main.added') }}: {!! $photo->user->getProfile() !!} ({{ dateFixed($photo->created_at) }})<br>
            <a href="/photos/comments/{{ $photo->id }}">{{ __('main.comments') }}</a> ({{ $photo->count_comments }})
            <a href="/photos/end/{{ $photo->id }}">&raquo;</a>
        </div>
    </div>

    <?php $nav = photoNavigation($photo->id); ?>

    @if ($nav['next'] || $nav['prev'])
        <div class="section shadow text-center font-weight-bold">
            @if ($nav['next'])
                <a href="/photos/{{ $nav['next'] }}">&laquo; {{ __('main.previous') }}</a> &nbsp;
            @endif

            @if ($nav['prev'])
                &nbsp; <a href="/photos/{{ $nav['prev'] }}">{{ __('main.next') }} &raquo;</a>
            @endif
        </div>
    @endif
@stop
