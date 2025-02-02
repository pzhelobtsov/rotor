<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UploadTrait;
use Exception;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;

/**
 * Class Down
 *
 * @property int id
 * @property int category_id
 * @property string title
 * @property string text
 * @property int user_id
 * @property int created_at
 * @property int count_comments
 * @property int rating
 * @property int loads
 * @property int active
 * @property int updated_at
 * @property Collection files
 * @property Collection comments
 * @property Load category
 */
class Down extends BaseModel
{
    use UploadTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Директория загрузки файлов
     *
     * @var string
     */
    public $uploadPath = '/uploads/files';

    /**
     * Counting field
     *
     * @var string
     */
    public $countingField = 'loads';

    /**
     * Список расширений доступных для просмотра в архиве
     *
     * @var array
     */
    public static $viewExt = ['xml', 'wml', 'asp', 'aspx', 'shtml', 'htm', 'phtml', 'html', 'php', 'htt', 'dat', 'tpl', 'htaccess', 'pl', 'js', 'jsp', 'css', 'txt', 'sql', 'gif', 'png', 'bmp', 'wbmp', 'jpg', 'jpeg', 'env', 'gitignore', 'json', 'yml', 'md'];

    /**
     * Morph name
     *
     * @var string
     */
    public static $morphName = 'downs';

    /**
     * Возвращает категорию загрузок
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Load::class, 'category_id')->withDefault();
    }

    /**
     * Возвращает комментарии
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'relate')->with('relate');
    }

    /**
     * Возвращает связь с голосованием
     *
     * @return morphOne
     */
    public function polling(): morphOne
    {
        return $this->morphOne(Polling::class, 'relate')->where('user_id', getUser('id'));
    }

    /**
     * Возвращает последнии комментарии к файлу
     *
     * @param int $limit
     * @return HasMany
     */
    public function lastComments(int $limit = 15): HasMany
    {
        return $this->hasMany(Comment::class, 'relate_id')
            ->where('relate_type', self::$morphName)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->limit($limit);
    }

    /**
     * Возвращает загруженные файлы
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'relate');
    }

    /**
     * Возвращает файлы
     *
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files->filter(static function (File $value, $key) {
            return ! $value->isImage();
        });
    }

    /**
     * Возвращает картинки
     *
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->files->filter(static function (File $value, $key) {
            return $value->isImage();
        });
    }

    /**
     * Возвращает сокращенный текст описания
     *
     * @param int $words
     * @return HtmlString
     */
    public function shortText(int $words = 50): HtmlString
    {
        if (strlen($this->text) > $words) {
            $this->text = bbCodeTruncate($this->text, $words);
        }

        return new HtmlString($this->text);
    }

    /**
     * Возвращает массив доступных расширений для просмотра в архиве
     *
     * @return array
     */
    public static function getViewExt(): array
    {
        return self::$viewExt;
    }

    /**
     * Загружает файл
     *
     * @param  UploadedFile $file
     * @return array
     */
    public function uploadAndConvertFile(UploadedFile $file): array
    {
        $uploadFile = $this->uploadFile($file);
        $this->convertVideo($uploadFile);

        return $uploadFile;
    }

    /**
     * Конвертирует видео
     *
     * @param array $file
     * @return void
     */
    public function convertVideo(array $file): void
    {
        $isVideo = strpos($file['mime'], 'video/') !== false;

        // Обработка видео
        if ($isVideo && config('ffmpeg.enabled')) {
            $ffconfig = [
                'ffmpeg.binaries'  => config('ffmpeg.path'),
                'ffprobe.binaries' => config('ffmpeg.ffprobe.path'),
                'ffmpeg.threads'   => config('ffmpeg.threads'),
                'timeout'          => config('ffmpeg.timeout'),
            ];

            // Сохраняем скрин с 5 секунды
            /*$ffmpeg = FFMpeg::create($ffconfig);
            $video = $ffmpeg->open(public_path($file['path']));

            $frame = $video->frame(TimeCode::fromSeconds(5));
            $frame->save(public_path($file['path'] . '.jpg'));

            $this->files()->create([
                'hash'       => $file['path'] . '.jpg',
                'name'       => 'screenshot.jpg',
                'size'       => filesize(public_path($file['path'] . '.jpg')),
                'user_id'    => getUser('id'),
                'created_at' => SITETIME,
            ]);*/

            // Перекодируем видео в h264
            $ffprobe = FFProbe::create($ffconfig);
            $video = $ffprobe
                ->streams(public_path($file['path']))
                ->videos()
                ->first();

            if ($video && $file['extension'] === 'mp4' && $video->get('codec_name') !== 'h264') {
                $format = new X264('libmp3lame', 'libx264');
                $video->save($format, public_path($file['path'] . '.convert'));

                rename(public_path($file['path'] . '.convert'), public_path($file['path']));
            }
        }
    }

    /**
     * Удаление загрузки и загруженных файлов
     *
     * @return bool|null
     * @throws Exception
     */
    public function delete(): ?bool
    {
        $this->files->each(static function (File $file) {
            $file->delete();
        });

        return parent::delete();
    }
}
