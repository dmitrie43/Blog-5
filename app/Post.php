<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; //Для изменения даты

class Post extends Model
{
    use Sluggable;

    protected $fillable = ['title','content', 'date', 'description']; //

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields)
    {
        $post = new static; //можем обращаться без создания экземпляра класса
        $post->fill($fields);
        $post->user_id = Auth::user()->id;
        $post->save();
        return $post;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete();
    }

    public function removeImage() {
        if ($this->image != null) {
            Storage::delete('uploads/' . $this->image);
        }
    }

    public function uploadImage($image)
    {
        if ($image == null) {
            return;
        }
        $this->removeImage();
        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function setCategory($id)
    {
        if ($id == null) {
            return;
        }
        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
        if ($ids == null) {
            return;
        }
        $this->tags()->sync($ids);
    }

    public function setDraft() {
        $this->status = 1;
        $this->save();
    }

    public function setPublic() {
        $this->status = 0;
        $this->save();
    }

    public function toggleStatus($value) {
        if ($value == null) {
            return $this->setDraft();
        }
        return $this->setPublic();
    }

    public function setFeatured() {
        $this->is_featured = 1;
        $this->save();
    }

    public function setStandart() {
        $this->is_featured = 0;
        $this->save();
    }

    public function toggleFeatured($value) {
        if ($value == null) {
            return $this->setStandart();
        }
        return $this->setFeatured();
    }

    public function getImage() {
        if ($this->image == null) { return '/img/no-image.png'; }
        return '/uploads/' . $this->image;
    }

    public function getCategoryTitle() {
        if ($this->category != null) {
            return $this->category->title;
        }
        return 'Нет категории';
    }

    public function getTagsTitles() {
        if (!$this->tags->isEmpty()) {
            return implode(', ', $this->tags->pluck('title')->all());
        }
        return 'Нет тегов';
    }

    public function getDate() {
        return Carbon::createFromFormat('Y-m-d', $this->date)->format('F d,Y');
    }

    public function hasPrevious() {
        return self::where('id', '<', $this->id)->max('id');
        //1,2,3,(4)<-   _5_
    }


    public function getPrevious() {
        $postID = $this->hasPrevious();
        return self::find($postID);
    }

    public function hasNext() {
        return self::where('id', '>', $this->id)->min('id');
        //_5_   ->(6),7,8
    }

    public function getNext() {
        $postID = $this->hasNext();
        return self::find($postID);
    }

    public function related() {
        return self::all()->except($this->id); //Вытащить все посты кроме текущего
    }

    public function hasCategory() {
        return $this->category != null ? true : false;
    }
}


