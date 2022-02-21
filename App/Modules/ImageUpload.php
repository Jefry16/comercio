<?php

namespace App\Modules;


class ImageUpload
{
  public static function singleImageNewProduct()
  {

    $error = 0;
    if (isset($_FILES['thumb']) && !$_FILES['thumb']['error']) {

      if ($_FILES['thumb']['size'] > 3145728) {

        $error = ['This file size can\'t be longer than 3 megabytes.'];

        return $error;
      }

      $mime_types = ['image/gif', 'image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];

      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $_FILES['thumb']['tmp_name']);

      if (!in_array($mime_type, $mime_types)) {

        $error = ['Invalid file.'];

        return $error;
      }

      $pathinfo = pathinfo($_FILES['thumb']["name"]);
      $base = $pathinfo['filename'];

      $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

      $base = mb_substr($base, 0, 200);

      $year = date('Y');

      $month = date('m');

      if (!is_dir(__DIR__ . '/../../public/uploads/' . $year)) {

        mkdir(__DIR__ . '/../../public/uploads/' . $year, 0777, true);
      }

      if (!is_dir(__DIR__ . '/../../public/uploads/' . $year . '/' . $month)) {

        mkdir(__DIR__ . '/../../public/uploads/' . $year . '/' . $month, 0777, true);
      }


      $filename = $year . '/' . $month . '/' . $base . "." . $pathinfo['extension'];

      $destination = dirname(__DIR__ . '/../../public/uploads/') . '/uploads/' . $filename;



      $i = 1;

      while (file_exists($destination)) {

        $filename = $year . '/' . $month . '/' . $base . "-$i." . $pathinfo['extension'];
        $destination = dirname($_SERVER['SERVER_NAME']) . '/uploads/' . $filename;

        $i++;
      }

      if (move_uploaded_file($_FILES['thumb']['tmp_name'], $destination)) {

        $error = $filename;

        return $error;
      } else {

        $error = ['The file for the thumbnail could not be uploaded.'];

        return $error;
      }
    }
    return $error;
  }

  public static function imagesFromNewProduct()
  {
    
    if (isset($_FILES['images'])) {

      $images = [];

      $mime_types = ['image/gif', 'image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];

      for ($i = 0; $i < count($_FILES['images']['name']); $i++) {

        if ($_FILES['images']['size'][$i] > 3145728) {
          continue;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime_type = '';

        if ($_FILES['images']['tmp_name'][$i]) {
          $mime_type = finfo_file($finfo, $_FILES['images']['tmp_name'][$i]);
        }

        if (!in_array($mime_type, $mime_types)) {

          continue;
        }

        $pathinfo = pathinfo($_FILES['images']["name"][$i]);
        $base = $pathinfo['filename'];

        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

        $base = mb_substr($base, 0, 200);

        $year = date('Y');

        $month = date('m');

        if (!is_dir(__DIR__ . '/../../public/uploads/' . $year)) {

          mkdir(__DIR__ . '/../../public/uploads/' . $year, 0777, true);
        }

        if (!is_dir(__DIR__ . '/../../public/uploads/' . $year . '/' . $month)) {

          mkdir(__DIR__ . '/../../public/uploads/' . $year . '/' . $month, 0777, true);
        }

        $filename = $year . '/' . $month . '/' . $base . "." . $pathinfo['extension'];

        $destination = dirname(__DIR__ . '/../../public/uploads/') . '/uploads/' . $filename;

        $j = 1;

        while (file_exists($destination)) {

          $filename = $year . '/' . $month . '/' . $base . "-$j." . $pathinfo['extension'];
          $destination = dirname($_SERVER['SERVER_NAME']) . '/uploads/' . $filename;

          $j++;
        }

        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $destination)) {

          $images[] = $filename;
        }
      }
      return json_encode($images, true);
    }
  }
}
