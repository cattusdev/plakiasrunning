<?php
class MediaHandler
{
    private $db;
    private $crud;
    private $input;
    private $validator;
    private $_data;
    private $_protected_folders = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
        $this->input = new Input();
        $this->validator = new Validate();

        $this->_protected_folders = [
            Config::get('app_root_public') . '/includes',
            Config::get('app_root_public') . '/core',
        ];
    }

    public function uploadImage($file, $max_size = 500000, $allowed_types = array("image/jpeg", "image/png", "image/gif"), $upload_dir = "secure_uploads/", $extraParam = null)
    {
        // Use strict type checking
        if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
            return "Error: Invalid file.";
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return "Error: No file uploaded.";
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "Error: File size too large.";
            default:
                return "Error: Unknown file error.";
        }

        if ($file['size'] > $max_size) {
            return "Error: File size too large.";
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return "Error: Invalid file.";
        }

        // Check that the uploaded file is an image
        $image_info = getimagesize($file['tmp_name']);
        if (
            $image_info === false
        ) {
            return "Error: Invalid image file.";
        }
        list($width, $height, $type, $attr) = $image_info;

        // Use a whitelist of allowed file types
        $mime_type = $image_info['mime'];
        if (!in_array($mime_type, $allowed_types)) {
            return "Error: Invalid file type.";
        }

        //get file extension
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array(strtolower($ext), $allowed_ext)) {
            return "Error: Invalid file extension.";
        }

        // Use a secure, random file name for the image
        $file_name = bin2hex(random_bytes(16)) . "." . $ext;
        $save_path = Config::get('app_root_public') . '/' . $upload_dir . $file_name;

        // Save the image to a secure directory on the server
        if (!is_dir(Config::get('app_root_public') . '/' . $upload_dir)) {
            mkdir(Config::get('app_root_public') . '/' . $upload_dir, 0750, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $save_path)) {
            return "Error: Failed to save image.";
        }

        // Create and save the thumbnail if the image is featured
        if ($extraParam == 'createThumbnail') {
            $thumbnail_path = Config::get('app_root_public') . '/' . $upload_dir . 'thumbnails/';
            if (!is_dir($thumbnail_path)) {
                mkdir($thumbnail_path, 0750, true);
            }

            $thumbnail_file_name = 'thumb_' . $file_name;
            $thumbnail_save_path = $thumbnail_path . $thumbnail_file_name;

            // Create the thumbnail
            if (!$this->createThumbnail($save_path, $thumbnail_save_path, 150, 150)) {
                return "Error: Failed to create thumbnail.";
            }

            // Return the paths of both the original image and thumbnail
            return [
                'original' => $upload_dir . $file_name,
                'thumbnail' => $upload_dir . 'thumbnails/' . $thumbnail_file_name
            ];
        }

        return $extraParam === 'returnFileName' ? $file_name : $upload_dir . $file_name;

        if ($extraParam == 'returnFileName') {
            return $file_name;
        }
        if ($extraParam == 'returnFilePath') {
            return $upload_dir . $file_name;
        }
        if ($extraParam == 'returnPath') {
            return Config::get('app_root_public') . '/' . $upload_dir;
        }
    }


    function deleteImage($filepath)
    {
        $filepath = Config::get('app_root_public') . '/' . $filepath;

        // Check if file exists
        if (!is_file($filepath)) {
            return "Error: File does not exist.";
        }
        // Check if file is an image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return "Error: Failed to open file info.";
        }
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        if (strpos($mime, 'image/') !== 0) {
            return "Error: File is not an image.";
        }
        //Check if file is already deleted 
        if (!file_exists($filepath)) {
            return "Error: File already deleted.";
        }
        //Check if the file is writable
        if (!is_writable($filepath)) {
            return "Error: File is not writable.";
        }
        // Attempt to delete the file
        if (!unlink($filepath)) {
            return "Error: Failed to delete file.";
        }
        return "File deleted successfully.";
    }


    public function uploadFile($file, $max_size = 5000000, $allowed_types = array('pdf', 'txt', 'doc'), $upload_dir = 'uploads/', $extraParam = null)
    {


        // Check for errors in the uploaded file
        if (!isset($file['error']) || is_array($file['error'])) {
            return "Error: Invalid file. " . $file['name'];
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return "Error: No file uploaded. " . $file['name'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "Error: File size too large. " . $file['name'];
            default:
                return "Error: Unknown file error. " . $file['name'];
        }

        // Check that the uploaded file is within the size limit
        if ($file['size'] > $max_size) {
            return "Error: File size too large. " . $file['name'];
        }

        // Use a whitelist of allowed file extensions
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed_types)) {
            return "Error: Invalid file type. " . $file['name'];
        }

        // Check that the MIME type of the uploaded file is allowed
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        $allowed_mime_types = array(
            'application/pdf',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
        if (!in_array($mime_type, $allowed_mime_types)) {
            return "Error: Invalid file type. " . $file['name'];
        }

        // Use a secure, random file name for the uploaded file
        $file_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $save_path = Config::get('app_root_public') . '/' . $upload_dir . $file_name;

        // Save the file to a secure directory on the server
        if (!is_dir(Config::get('app_root_public') . '/' . $upload_dir)) {
            mkdir(Config::get('app_root_public') . '/' . $upload_dir, 0750, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $save_path)) {
            return "Error: Failed to save file. " . $file['name'];
        }

        // return $upload_dir . $file_name;

        if ($extraParam == 'returnFileName') {
            return $file_name;
        }
        if ($extraParam == 'returnFilePath') {
            return $upload_dir . $file_name;
        }
        if ($extraParam == 'returnPath') {
            return Config::get('app_root_public') . '/' . $upload_dir;
        }
    }


    function deleteFile($filepath)
    {
        $filepath = Config::get('app_root_public') . '/' . $filepath;

        // Check if file's path is protected
        if ($this->is_path_protected($filepath)) {
            return "Error: File path is protected and cannot be deleted.";
        }

        // Check if file exists
        if (!is_file($filepath)) {
            return "Error: File does not exist.";
        }
        // Check if file is one of the allowed types
        $allowed_extensions = array('pdf', 'txt', 'doc');
        $file_extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            return "Error: File type not allowed.";
        }
        //Check if file is already deleted 
        if (!file_exists($filepath)) {
            return "Error: File already deleted.";
        }
        //Check if the file is writable
        if (!is_writable($filepath)) {
            return "Error: File is not writable.";
        }
        // Attempt to delete the file
        if (!unlink($filepath)) {
            return "Error: Failed to delete file.";
        }
        return "File deleted successfully.";
    }


    // private $_protected_folders = array(
    //     Config::get('app_root_public') . '/includes',
    //     Config::get('app_root_public') . '/core',
    // );

    function is_path_protected($path)
    {
        $path = realpath($path);
        foreach ($this->_protected_folders as $folder) {
            $folder = realpath($folder);
            if (strpos($path, $folder) === 0) {
                return true;
            }
        }
        return false;
    }

    function formatBytes($bytes, $decimals = 2)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $i = floor(log($bytes) / log($k));
        return number_format($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }


    public function reArrayFiles($files)
    {
        $file_ary = array();
        $file_count = count($files['name']);
        $file_keys = array_keys($files);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $files[$key][$i];
            }
        }

        return $file_ary;
    }

    public function createThumbnail($source_path, $thumbnail_path, $thumb_width, $thumb_height)
    {
        $image_info = getimagesize($source_path);
        list($width_orig, $height_orig) = $image_info;

        switch ($image_info['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source = imagecreatefrompng($source_path);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($source_path);
                break;
            case 'image/webp':
                $source = imagecreatefromgif($source_path);
                break;
            default:
                return false;
        }

        // Calculate aspect ratios
        $aspect_ratio_orig = $width_orig / $height_orig;
        $aspect_ratio_thumb = $thumb_width / $thumb_height;

        if ($aspect_ratio_thumb > $aspect_ratio_orig) {
            // Thumbnail is wider than image
            $new_height = $thumb_height;
            $new_width = $thumb_height * $aspect_ratio_orig;
        } else {
            // Thumbnail is taller than image
            $new_width = $thumb_width;
            $new_height = $thumb_width / $aspect_ratio_orig;
        }

        // Resize the image
        $temp_thumb = imagecreatetruecolor($new_width, $new_height);

        // Preserve transparency for PNG and GIF images
        if ($image_info['mime'] == 'image/png' || $image_info['mime'] == 'image/gif') {
            imagecolortransparent($temp_thumb, imagecolorallocatealpha($temp_thumb, 0, 0, 0, 127));
            imagealphablending($temp_thumb, false);
            imagesavealpha($temp_thumb, true);
        }

        imagecopyresampled($temp_thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);

        // Calculate coordinates to center the image
        $x = ($new_width - $thumb_width) / 2;
        $y = ($new_height - $thumb_height) / 2;

        // Create the final thumbnail image
        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // Preserve transparency for PNG and GIF images
        if ($image_info['mime'] == 'image/png' || $image_info['mime'] == 'image/gif') {
            imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Copy and crop the temporary thumbnail into the final thumbnail
        imagecopyresampled(
            $thumb,
            $temp_thumb,
            0,
            0,
            $x,
            $y,
            $thumb_width,
            $thumb_height,
            $thumb_width,
            $thumb_height
        );

        // Save the thumbnail
        switch ($image_info['mime']) {
            case 'image/jpeg':
                imagejpeg($thumb, $thumbnail_path, 90); // 90 is the quality percentage
                break;
            case 'image/png':
                imagepng($thumb, $thumbnail_path);
                break;
            case 'image/gif':
                imagegif($thumb, $thumbnail_path);
                break;
        }

        // Clean up
        imagedestroy($temp_thumb);
        imagedestroy($thumb);
        imagedestroy($source);

        return true;
    }


    public function data()
    {
        return $this->_data;
    }
}
