<?php

namespace Modules\Iblog\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Iblog\Presenters\PostPresenter;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Tag\Contracts\TaggableInterface;
use Modules\Tag\Traits\TaggableTrait;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Media\Entities\File;

class Post extends Model implements TaggableInterface
{
    use Translatable,PresentableTrait, NamespacedEntity, TaggableTrait, MediaRelation;

    protected static $entityNamespace = 'asgardcms/post';

    protected $table = 'iblog__posts';

    protected $fillable = ['title','description','slug','summary','metatitle','metadescription','metakeywords','translatableoption','options','category_id','user_id','status','created_at'];
    public $translatedAttributes = ['title','description','slug','summary','metatitle','metadescription','metakeywords','translatableoption'];
    protected $presenter = PostPresenter::class;
    protected $fakeColumns = ['options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'iblog__post__category');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function user()
    {
        $driver = config('asgard.user.config.driver');

        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }

    public function getOptionsAttribute($value) {
        return json_decode(json_decode($value));

    }

    public function getSecundaryimageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'secundaryimage')->first()->path??null;

        if ($thumbnail === null) {
            return 'modules/iblog/img/post/default.jpg';
        }

        return $thumbnail;
    }
    public function getMainimageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'mainimage')->first()->path??null;

        if ($thumbnail === null) {
            if(isset($this->options->mainimage)){
                return $this->options->mainimage;
            }
            return 'modules/iblog/img/post/default.jpg';
        }

        return $thumbnail;
    }
    public function getGalleryAttibute(){

        return $this->filesByZone('gallery')->get();
    }

    /**
     * URL post
     * @return string
     */
    public function getUrlAttribute() {

        if(!isset($this->category->slug)) {
            $this->category = Category::take(1)->get()->first();
        }

        return \URL::route(\LaravelLocalization::getCurrentLocale() . '.iblog.'.$this->category->slug.'.slug', [$this->slug]);

    }

    /**
     * Magic Method modification to allow dynamic relations to other entities.
     * @var $value
     * @var $destination_path
     * @return string
     */
    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.iblog.config.relations', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

}
