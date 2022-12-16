<?php

namespace App\Models;

use App\Models\SiteModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SiteBaseSetting extends SiteModel
{
    protected $table = "site_base_settings";

    protected $fillable = [ 'enabled', 'title', 'desc', 'favicon', 'theme', 'custom_theme_color', 'banner', 'floating_window', 'popup_window' ];
    
    protected $casts = [
        'enabled' => 'boolean',
        'floating_window' => 'json',
        'popup_window' => 'json'
    ];

    public function setFaviconAttribute($value)
    {
        if (isset($value) && $value instanceof UploadedFile) {
            $this->attributes['favicon'] = Storage::disk('admin')->putFile('images', $value);
        }
    }

    public function setBannerAttribute($value)
    {
        if (isset($value) && $value instanceof UploadedFile) {
            $this->attributes['banner'] = Storage::disk('admin')->putFile('images', $value);
        }
    }

    public function setFloatingWindowAttribute($value) {
        
        $oldVal = isset($this->attributes['floating_window'])
                    ? json_decode($this->attributes['floating_window'], true)
                    : [];

        if (isset($value['image']) && $value['image'] instanceof UploadedFile) {
            $value['image'] = Storage::disk('admin')->putFile('images', $value['image']);
        }

        $this->attributes['floating_window'] = json_encode(array_merge($oldVal, $value));
    }

    public function setPopupWindowAttribute($value) {

        $oldVal = isset($this->attributes['popup_window']) 
                ? json_decode($this->attributes['popup_window'], true)
                : [];

        if (isset($value['image']) && $value['image'] instanceof UploadedFile) {
            $value['image'] = Storage::disk('admin')->putFile('images', $value['image']);
        }

        $this->attributes['popup_window'] = json_encode(array_merge($oldVal, $value));
    }
}
