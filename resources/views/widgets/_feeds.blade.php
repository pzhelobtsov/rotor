@if ($posts->isNotEmpty())
    @foreach ($posts as $post)

        {{-- Новости --}}
        @if ($post instanceof \App\Models\News)
            <div class="section mb-3 shadow">
                <h3><a class="post-title" href="/news/{{ $post->id }}">{{ $post->title }}</a></h3>

                <div class="section-content">
                    <div class="section-message row mb-3">
                        @if ($post->image)
                            <div class="col-sm-3 mb-3">
                                <a href="{{ $post->image }}" class="gallery">{{ resizeImage($post->image, ['class' => 'img-thumbnail img-fluid', 'alt' => $post->title]) }}</a>
                            </div>
                        @endif

                        <div class="col">
                            {{ bbCode($post->text) }}
                        </div>
                    </div>
                </div>

                <div class="section-body">
                    {{ __('main.added') }}: {{ $post->user->getProfile() }} <small class="section-date text-muted fst-italic">{{ dateFixed($post->created_at) }}</small>

                    <div class="js-rating">
                        {{ __('main.rating') }}:
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-down<?= $post->vote === '-' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="-" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-down"></i></a>
                        @endif
                        <b>{{ formatNum($post->rating) }}</b>
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-up<?= $post->vote === '+' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="+" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-up"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Посты --}}
        @if ($post instanceof \App\Models\Topic && $post->lastPost->id)
            <div class="section mb-3 shadow">
                <h3><a class="post-title" href="/topics/{{ $post->id }}">{{ $post->title }}</a></h3>

                <div class="user-avatar">
                    {{ $post->lastPost->user->getAvatar() }}
                    {{ $post->lastPost->user->getOnline() }}
                </div>

                <div class="section-user d-flex align-items-center">
                    <div class="flex-grow-1">
                        {{ $post->lastPost->user->getProfile() }}
                        <small class="section-date text-muted fst-italic">{{ dateFixed($post->lastPost->created_at) }}</small>
                        <br>
                        <small class="fst-italic">{{ $post->lastPost->user->getStatus() }}</small>
                    </div>

                    <div class="text-end">
                        <div class="js-rating">
                            @if (getUser() && getUser('id') !== $post->user_id)
                                <a class="post-rating-down{{ $post->vote === '-' ? ' active' : '' }}" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->lastPost->getMorphClass() }}" data-vote="-" data-token="{{ csrf_token() }}"><i class="fas fa-arrow-down"></i></a>
                            @endif
                            <b>{{ formatNum($post->rating) }}</b>
                            @if (getUser() && getUser('id') !== $post->user_id)
                                <a class="post-rating-up{{ $post->vote === '+' ? ' active' : '' }}" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->lastPost->getMorphClass() }}" data-vote="+" data-token="{{ csrf_token() }}"><i class="fas fa-arrow-up"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="section-body border-top">
                    <div class="section-message">
                        {{ $post->lastPost->text ? bbCode($post->lastPost->text) : 'Удалено' }}
                    </div>

                    @if ($post->lastPost->files->isNotEmpty())
                        <div class="section-media">
                            <i class="fa fa-paperclip"></i> <b>{{ __('main.attached_files') }}:</b><br>
                            @foreach ($post->lastPost->files as $file)
                                <div class="media-file">
                                    @if ($file->isImage())
                                        <a href="{{ $file->hash }}" class="gallery" data-group="{{ $post->id }}">{{ resizeImage($file->hash, ['alt' => $file->name]) }}</a><br>
                                    @endif

                                    @if ($file->isAudio())
                                        <div>
                                            <audio src="{{ $file->hash }}" style="max-width:100%;" preload="metadata" controls></audio>
                                        </div>
                                    @endif
                                    {{ icons($file->extension) }}
                                    <a href="{{ $file->hash }}">{{ $file->name }}</a> ({{ formatSize($file->size) }})
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($post->edit_user_id)
                        <div class="small">
                            <i class="fa fa-exclamation-circle text-danger"></i> {{ __('main.changed') }}: {{ $post->editUser->getName() }} ({{ dateFixed($post->updated_at) }})
                        </div>
                    @endif

                    @if (isAdmin())
                        <div class="small text-muted fst-italic mt-2">{{ $post->brow }}, {{ $post->ip }}</div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Фото --}}
        @if ($post instanceof \App\Models\Photo)
            <div class="section mb-3 shadow">
                <div class="section-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="section-title">
                            <h3><a class="post-title" href="/photos/{{ $post->id }}">{{ $post->title }}</a></h3>
                        </div>
                    </div>

                    <div class="text-end js-rating">
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-down<?= $post->vote === '-' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="-" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-down"></i></a>
                        @endif
                        <b>{{ formatNum($post->rating) }}</b>
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-up<?= $post->vote === '+' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="+" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-up"></i></a>
                        @endif
                    </div>
                </div>

                <div class="section-content">
                    @include('app/_carousel', ['model' => $post, 'path' => '/photos'])

                    @if ($post->text)
                        {{ bbCode($post->text) }}<br>
                    @endif

                    {{ __('main.added') }}: {{ $post->user->getProfile() }} ({{ dateFixed($post->created_at) }})<br>
                    <a href="/photos/comments/{{ $post->id }}">{{ __('main.comments') }}</a> ({{ $post->count_comments }})
                    <a href="/photos/end/{{ $post->id }}">&raquo;</a>
                </div>
            </div>
        @endif

        {{-- Загрузки --}}
        @if ($post instanceof \App\Models\Down)
        <div class="section mb-3 shadow">
            <h3><a class="post-title" href="/downs/{{ $post->id }}">{{ $post->title }}</a></h3>

            @if ($post->getImages()->isNotEmpty())
                @include('app/_carousel', ['model' => $post, 'files' => $post->getImages(), 'path' => '/downs'])
            @endif

            <div class="section-message mb-3">
                {{ bbCode($post->text) }}
            </div>

            @if ($post->getFiles()->isNotEmpty())
                @foreach ($post->getFiles() as $file)
                    <div class="media-file mb-3">
                        @if ($file->hash && file_exists(public_path($file->hash)))

                            @if ($file->extension === 'mp3')
                                <div>
                                    <audio src="{{ $file->hash }}" style="max-width:100%;" preload="metadata" controls controlsList="{{ $allowDownload ? null : 'nodownload' }}"></audio>
                                </div>
                            @endif

                            @if ($file->extension === 'mp4')
                                <div>
                                    <video src="{{ $file->hash }}" style="max-width:100%;" preload="metadata" controls playsinline controlsList="{{ $allowDownload ? null : 'nodownload' }}"></video>
                                </div>
                            @endif

                            <b>{{ $file->name }}</b> ({{ formatSize($file->size) }})<br>
                            @if ($file->extension === 'zip')
                                <a href="/downs/zip/{{ $file->id }}">{{ __('loads.view_archive') }}</a><br>
                            @endif

                            @if ($allowDownload)
                                <a class="btn btn-success" href="/downs/download/{{ $file->id }}"><i class="fa fa-download"></i> {{ __('main.download') }}</a><br>
                            @endif
                        @else
                            <i class="fa fa-download"></i> {{ __('main.file_not_found') }}
                        @endif
                    </div>
                @endforeach

                @if (! $allowDownload)
                    {{ showError(__('loads.download_authorized')) }}
                @endif
            @else
                {{ showError(__('main.not_uploaded')) }}
            @endif

            <div class="mb-3">
                <i class="fa fa-comment"></i> <a href="/downs/comments/{{ $post->id }}">{{ __('main.comments') }}</a> ({{ $post->count_comments }})
                <a href="/downs/end/{{ $post->id }}">&raquo;</a><br>

                {{ __('main.rating') }}: {{ ratingVote($post->getCalculatedRating()) }}<br>
                {{ __('main.votes') }}: <b>{{ $post->rated }}</b><br>
                {{ __('main.downloads') }}: <b>{{ $post->loads }}</b><br>
                {{ __('main.author') }}: {{ $post->user->getProfile() }} ({{ dateFixed($post->created_at) }})
            </div>
        </div>
        @endif

        {{-- Статьи --}}
        @if ($post instanceof \App\Models\Article)
            <div class="section mb-3 shadow">
                <div class="section-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="section-title">
                            <h3><a class="post-title" href="/articles/{{ $post->id }}">{{ $post->title }}</a></h3>
                        </div>
                    </div>

                    <div class="text-end js-rating">
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-down<?= $post->vote === '-' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="-" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-down"></i></a>
                        @endif
                        <b>{{ formatNum($post->rating) }}</b>
                        @if (getUser() && getUser('id') !== $post->user_id)
                            <a class="post-rating-up<?= $post->vote === '+' ? ' active' : '' ?>" href="#" onclick="return changeRating(this);" data-id="{{ $post->id }}" data-type="{{ $post->getMorphClass() }}" data-vote="+" data-token="{{ csrf_token() }}"><i class="fa fa-thumbs-up"></i></a>
                        @endif
                    </div>
                </div>

                <div class="section-content">
                    {{ $post->shortText() }}
                    {{ __('main.author') }}: {{ $post->user->getProfile() }} ({{ dateFixed($post->created_at) }})<br>
                    {{ __('main.views') }}: {{ $post->visits }}<br>
                    <a href="/articles/comments/{{ $post->id }}">{{ __('main.comments') }}</a> ({{ $post->count_comments }})
                    <a href="/articles/end/{{ $post->id }}">&raquo;</a>
                </div>
            </div>
        @endif

        {{-- Статьи --}}
        @if ($post instanceof \App\Models\Item)
            <div class="section mb-3 shadow">
                <h3><a class="post-title" href="/items/{{ $post->id }}">{{ $post->title }}</a></h3>

                <div class="col-md-12">
                    @if ($post->files->isNotEmpty())
                        <div class="row">
                            <div class="col-md-12">
                                @include('app/_carousel', ['model' => $post])
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-10">
                            <div class="section-message mb-3">
                                {{ bbCode($post->text) }}
                            </div>
                            <div>
                                @if ($post->phone)
                                    <span class="badge rounded-pill bg-primary mb-3">{{ __('boards.phone') }}: {{ $post->phone }}</span><br>
                                @endif

                                <i class="fa fa-user-circle"></i> {{ $post->user->getProfile() }} / {{ dateFixed($post->updated_at) }}<br>
                            </div>
                        </div>

                        <div class="col-md-2">
                            @if ($post->price)
                                <button type="button" class="btn btn-info">{{ $post->price }} {{ setting('currency') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
