<?php

namespace App\Modules;


class ImageUpload
{
  public static function singleImageFromProduct($name)
  {
    echo is_dir(__DIR__ . '/../../public/uploads');
    $error = 0;

    if (isset($_FILES[$name]) && !$_FILES[$name]['error']) {

      if ($_FILES[$name]['size'] > 3145728) {

        $error = ['This file size can\'t be longer than 3 megabytes.'];

        return $error;
      }

      $mime_types = ['image/gif', 'image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];

      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $_FILES[$name]['tmp_name']);

      if (!in_array($mime_type, $mime_types)) {

        $error = ['Invalid file.'];

        return $error;
      }

      $pathinfo = pathinfo($_FILES[$name]["name"]);
      $base = $pathinfo['filename'];

      $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

      $base = mb_substr($base, 0, 200);

      $filename = $base . "." . $pathinfo['extension'];

      $destination = dirname($_SERVER['SERVER_NAME']) . '/uploads/' . $filename;



      $i = 1;

      while (file_exists($destination)) {

        $filename = $base . "-$i." . $pathinfo['extension'];
        $destination = dirname($_SERVER['SERVER_NAME']) . '/uploads/' . $filename;

        $i++;
      }

      if (move_uploaded_file($_FILES['$name']['tmp_name'], $destination)) {
        $error = $filename;

        return $error;
      } else {
        $error = ['No se pudo subir el archivo'];
        return $error;
      }
    }
    return $error;
  }
}
