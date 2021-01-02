@extends('layout')

@section('title', __('loads.top_downs') . ' (' . __('main.page_num', ['page' => $downs->currentPage()]) . ')')

@section('header')
    <h1>{{ __('loads.top_downs') }}</h1>
@stop

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/loads">{{ __('index.loads') }}</a></li>
            <li class="breadcrumb-item active">{{ __('loads.top_downs') }}</li>
        </ol>
    </nav>
@stop

@section('content')
    {{ __('main.sort') }}:
    <?php $active = ($order === 'loads') ? 'success' : 'light'; ?>
    <a href="/loads/top?sort=loads" class="badge badge-{{ $active }}">{{ __('main.downloads') }}</a>

    <?php $active = ($order === 'rated') ? 'success' : 'light'; ?>
    <a href="/loads/top?sort=rated" class="badge badge-{{ $active }}">{{ __('main.rating') }}</a>

    <?php $active = ($order === 'count_comments') ? 'success' : 'light'; ?>
    <a href="/loads/top?sort=comments" class="badge badge-{{ $active }}">{{ __('main.comments') }}</a>
    <hr>

    @if ($downs->isNotEmpty())
        @foreach ($downs as $data)
            <?php $rating = $data->rated ? round($data->rating / $data->rated, 1) : 0; ?>

            <div class="section mb-3 shadow">
                <div class="section-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="section-title">
                            <i class="fa fa-file"></i>
                            <a href="/downs/{{ $data->id }}">{{ $data->title }}</a>
                        </div>
                    </div>

                    <div class="text-right js-rating">
                        <b>{!! formatNum($rating) !!}</b>
                    </div>
                </div>

                <div class="section-content">
                    {{ __('loads.load') }}: <a href="/loads/{{ $data->category->id }}">{{ $data->category->name }}</a><br>
                    {{ __('main.downloads') }}: {{ $data->loads }}<br>
                    <a href="/downs/comments/{{ $data->id }}">{{ __('main.comments') }}</a> ({{ $data->count_comments }})
                    <a href="/downs/end/{{ $data->id }}">&raquo;</a>
                </div>
            </div>
        @endforeach
    @else
        {!! showError(__('loads.empty_downs')) !!}
    @endif

    {{ $downs->links() }}
@stop
