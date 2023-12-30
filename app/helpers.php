<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function uploadBase64Image($base64Image) {
    $decoder = new Base64ImageDecoder($base64Image, $allowedFormats = ['jpeg', 'png', 'jpg']);
    $decodedContent = $decoder->getDecodedContent();
    $format = $decoder->getFormat();
    $image = Str::random(10).'.'.$format;
    Storage::disk('public')->put($image, $decodedContent);

    return $image;
}

function getUser($param) {
    $user = User::where('id', $param)
                    ->orWhere('email', $param)
                    ->first();

    $user->profile_picture = $user->profile_picture ?
        url('storage/'.$user->profile_picture) : "";
    $user->ktp = $user->ktp ?
        url('storage/'.$user->ktp) : "";

    return $user;
}
