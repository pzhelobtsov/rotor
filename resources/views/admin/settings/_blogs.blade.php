@section('header')
    <h1>{{ trans('settings.blogs') }}</h1>
@stop

<form action="/admin/settings?act=blogs" method="post">
    @csrf
    <div class="form-group{{ hasError('sets[blogpost]') }}">
        <label for="blogpost">{{ trans('settings.blogs_per_page') }}:</label>
        <input type="number" class="form-control" id="blogpost" name="sets[blogpost]" maxlength="2" value="{{ getInput('sets.blogpost', $settings['blogpost']) }}" required>
        <div class="invalid-feedback">{{ textError('sets[blogpost]') }}</div>
    </div>

    <div class="form-group{{ hasError('sets[blogcomm]') }}">
        <label for="blogcomm">{{ trans('settings.blogs_comments') }}:</label>
        <input type="number" class="form-control" id="blogcomm" name="sets[blogcomm]" maxlength="2" value="{{ getInput('sets.blogcomm', $settings['blogcomm']) }}" required>
        <div class="invalid-feedback">{{ textError('sets[blogcomm]') }}</div>
    </div>

    <div class="form-group{{ hasError('sets[bloggroup]') }}">
        <label for="bloggroup">{{ trans('settings.blogs_groups') }}:</label>
        <input type="number" class="form-control" id="bloggroup" name="sets[bloggroup]" maxlength="2" value="{{ getInput('sets.bloggroup', $settings['bloggroup']) }}" required>
        <div class="invalid-feedback">{{ textError('sets[bloggroup]') }}</div>
    </div>

    <div class="form-group{{ hasError('sets[maxblogpost]') }}">
        <label for="maxblogpost">{{ trans('settings.blogs_symbols') }}:</label>
        <input type="number" class="form-control" id="maxblogpost" name="sets[maxblogpost]" maxlength="6" value="{{ getInput('sets.maxblogpost', $settings['maxblogpost']) }}" required>
        <div class="invalid-feedback">{{ textError('sets[maxblogpost]') }}</div>
    </div>

    <div class="form-group{{ hasError('sets[blogvotepoint]') }}">
        <label for="blogvotepoint">{{ trans('settings.blogs_points') }}:</label>
        <input type="number" class="form-control" id="blogvotepoint" name="sets[blogvotepoint]" maxlength="3" value="{{ getInput('sets.blogvotepoint', $settings['blogvotepoint']) }}" required>
        <div class="invalid-feedback">{{ textError('sets[blogvotepoint]') }}</div>
    </div>

    <div class="custom-control custom-checkbox">
        <input type="hidden" value="0" name="sets[blog_create]">
        <input type="checkbox" class="custom-control-input" value="1" name="sets[blog_create]" id="blog_create"{{ getInput('sets.blog_create', $settings['blog_create']) ? ' checked' : '' }}>
        <label class="custom-control-label" for="blog_create">{{ trans('settings.blogs_publish') }}</label>
    </div>

    <button class="btn btn-primary">{{ trans('main.save') }}</button>
</form>
