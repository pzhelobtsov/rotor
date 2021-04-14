@extends('layout')

@section('title', __('main.comments') . ' ' . $user->getName() . ' (' . __('main.page_num', ['page' => $comments->currentPage()]) . ')')

@section('header')
    <h1>{{ __('main.comments') }} {{ $user->getName() }}</h1>
@stop

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/photos">{{ __('index.photos') }}</a></li>
            <li class="breadcrumb-item active">{{ __('main.comments') }} {{ $user->getName() }}</li>
        </ol>
    </nav>
@stop

@section('content')
    @if ($comments->isNotEmpty())
        @foreach ($comments as $comment)
            <div class="section mb-3 shadow">
                <i class="fa fa-file-alt"></i> <b><a href="/photos/comment/{{ $comment->relate_id }}/{{ $comment->id }}">{{ $comment->title }}</a></b>

                @if (isAdmin())
                    <a href="#" class="float-right" onclick="return deleteComment(this)" data-rid="{{ $comment->relate_id }}" data-id="{{ $comment->id }}" data-type="{{ $comment->relate->getMorphClass() }}" data-token="{{ $_SESSION['token'] }}" data-toggle="tooltip" title="{{ __('main.delete') }}"><i class="fa fa-times"></i></a>
                @endif

                <div class="section-message">
                    {{ bbCode($comment->text) }}<br>

                    {{ __('main.posted') }}: {{ $comment->user->getProfile() }}
                    <small class="section-date text-muted font-italic">{{ dateFixed($comment->created_at) }}</small>

                    @if (isAdmin())
                        <div class="small text-muted font-italic mt-2">{{ $comment->brow }}, {{ $comment->ip }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        {{ showError(__('main.empty_comments')) }}
    @endif

    {{ $comments->links() }}
@stop
