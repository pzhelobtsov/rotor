@extends('layout')

@section('title')
    Кто в онлайне
@stop

@section('content')

    <h1>Кто в онлайне</h1>

    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Кто в онлайне</li>
        </ol>
    </nav>

    Всего на сайте: <b>{{ $all }}</b><br>
    Авторизованных:  <b>{{ $total }}</b><br><br>


    @if ($online->isNotEmpty())

        @foreach ($online as $data)
            <div class="b">
                <div class="img">
                    {!! userAvatar($data->user) !!}
                </div>

                <b>{!! profile($data->user) !!}</b> (Время: {{ dateFixed($data['updated_at'], 'H:i:s') }})
            </div>

            @if (isAdmin())
                <div>
                    <span class="data">({{ $data['brow'] }}, {{ $data['ip'] }})</span>
                </div>
            @endif
        @endforeach
        {!! pagination($page) !!}
    @else
        {!! showError('Пользователей нет!') !!}
    @endif

    <i class="fa fa-users"></i>

    @if ($guests)
        <a href="/online">Скрыть гостей</a><br>
    @else
        <a href="/online/all">Показать гостей</a><br>
    @endif
@stop
