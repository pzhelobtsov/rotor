@extends('layout')

@section('title')
    {{ __('index.messages') }}
@stop

@section('breadcrumb')
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/menu">{{ __('main.menu') }}</a></li>
            <li class="breadcrumb-item">{{ __('index.messages') }}</li>
        </ol>
    </nav>
@stop

@section('content')
    @if ($messages->isNotEmpty())
        @foreach ($messages as $data)
            <?php $login = $data->author->exists ? $data->author->login : $data->author_id ?>
            <div class="media border-bottom p-2 message-block" data-href="/messages/talk/{{ $login }}">
                <div class="img mr-3">
                    @if ($data->author_id)
                        {!! $data->author->getAvatar() !!}
                        {!! $data->author->getOnline() !!}
                    @else
                        <img class="avatar" src="/assets/img/images/avatar_system.png" alt="">
                        <div class="online bg-success" title="Online"></div>
                    @endif
                </div>
                <div class="media-body">
                    <div class="text-muted float-right">
                        {{  dateFixed($data->created_at) }}

                        @if ($data->type === 'out')
                            <i class="fas fa-xs {{ $data->recipient_read ? 'fa-check-double' : 'fa-check' }} text-success"></i>
                        @endif
                    </div>

                    @if ($data->author_id)
                        <b>{!! $data->author->getProfile() !!}</b>
                    @else
                        <b>{{ __('messages.system') }}</b>
                    @endif

                    <div class="message">
                        {{ $data->type === 'out' ? __('messages.you') . ': ' : '' }}
                        {!! bbCodeTruncate($data->text) !!}
                    </div>
                    @unless ($data->reading)
                        <span class="badge badge-info">{{ __('messages.new') }}</span>
                    @endunless
                </div>
            </div>
        @endforeach
    @else
        {!! showError(__('main.empty_messages')) !!}
    @endif

    {{ $messages->links() }}

    <i class="fa fa-search"></i> <a href="/searchusers">{{ __('index.user_search') }}</a><br>
    <i class="fa fa-address-book"></i> <a href="/contacts">{{ __('index.contacts') }}</a> / <a href="/ignores">{{ __('index.ignores') }}</a><br>
@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.media').on('click', function() {
                window.location = $(this).data('href');
                return false;
            }).find('a').on('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>
@endpush
