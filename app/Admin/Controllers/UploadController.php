<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
  public function upload(Request $request)
  {
    $urls = [];

    foreach ($request->file() as $file) {
      $urls[] = Storage::url($file->store('uploads/images'));
    }

    return [
      "errno" => 0,
      "data"  => $urls,
    ];
  }
}
