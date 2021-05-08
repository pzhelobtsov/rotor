@extends('layout')

@section('title', __('forums.title_create'))

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/forums">{{ __('index.forums') }}</a></li>
            <li class="breadcrumb-item active">{{ __('forums.title_create') }}</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="section-form mb-3 shadow">
        <form action="/forums/create" method="post">
            @csrf
            <div class="mb-3{{ hasError('fid') }}">
                <label for="inputForum" class="form-label">{{ __('forums.forum') }}:</label>
                <select class="form-select" id="inputForum" name="fid">

                    @foreach ($forums as $data)
                        <option value="{{ $data->id }}"{{ $fid === $data->id  && ! $data->closed ? ' selected' : '' }}{{ $data->closed ? ' disabled' : '' }}>{{ $data->title }}</option>

                        @if ($data->children->isNotEmpty())
                            @foreach ($data->children as $datasub)
                                <option value="{{ $datasub->id }}"{{ $fid === $datasub->id  && ! $datasub->closed ? ' selected' : '' }}{{ $datasub->closed ? ' disabled' : '' }}>– {{ $datasub->title }}</option>
                            @endforeach
                        @endif
                    @endforeach

                </select>
                <div class="invalid-feedback">{{ textError('fid') }}</div>
            </div>

            <div class="mb-3{{ hasError('title') }}">
                <label for="inputTitle" class="form-label">{{ __('forums.topic') }}:</label>
                <input name="title" class="form-control" id="inputTitle" maxlength="50" placeholder="{{ __('forums.topic') }}" value="{{ getInput('title') }}" required>
                <div class="invalid-feedback">{{ textError('title') }}</div>
            </div>

            <div class="mb-3{{ hasError('msg') }}">
                <label for="msg" class="form-label">{{ __('forums.post') }}:</label>
                <textarea class="form-control markItUp" maxlength="{{ setting('forumtextlength') }}" id="msg" rows="5" name="msg" required>{{ getInput('msg') }}</textarea>
                <div class="invalid-feedback">{{ textError('msg') }}</div>
                <span class="js-textarea-counter"></span>
            </div>

            <?php $checkVote = getInput('vote') ? true : false; ?>
            <?php $checked = $checkVote ? ' checked' : ''; ?>
            <?php $display = $checkVote ? '' : ' style="display: none"'; ?>

            <label>
                <input name="vote" onchange="return showVoteForm();" type="checkbox"{!! $checked !!}> {{ __('forums.create_vote') }}
            </label><br>

            <div class="js-vote-form"{!! $display !!}>
                <div class="mb-3{{ hasError('question') }}">

                    <label for="inputQuestion" class="form-label">{{ __('forums.question') }}:</label>
                    <input type="text" name="question" class="form-control" id="inputQuestion" value="{{ getInput('question') }}" maxlength="100">
                    <div class="invalid-feedback">{{ textError('question') }}</div>
                </div>

                @include('votes/_answers')
            </div>
            <button class="btn btn-primary">{{ __('forums.create_topic') }}</button>
        </form>
    </div>

    {{ __('forums.create_rule1') }}<br>
    <a href="/rules">{{ __('main.rules') }}</a><br>
    {{ __('forums.create_rule2') }}<br>
    <a href="/forums/search">{{ __('main.search') }}</a><br>
    {{ __('forums.create_rule3') }}<br><br>
@stop
