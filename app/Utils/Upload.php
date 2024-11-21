<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

/**
 * Simple PHP upload class
 * https://github.com/aivis/PHP-file-upload-class
 * @author Aivis Silins
 */
class Upload
{

    const AvailableMimeTypes = [
        'application/pdf' => 'pdf',
        'text/plain' => 'txt',

        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',

        'application/excel' => 'xls',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.ms-office' => 'xls',
        'application/x-excel' => 'xls',
        'application/x-msexcel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-excel.sheet.macroEnabled.12' => 'xlsm',

        'application/x-rar-compressed' => 'rar',
        'application/zip' => 'zip',
        'application/cdr' => 'cdr',
        'application/x-rar' => 'rar',
        'application/x-zip-compressed' => 'zip',

        'image/png' =>'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpeg',
        'image/gif' => 'gif',
        'image/bmp' => 'bmp',

        'audio/mpeg' => 'mp3',
        'audio/mp3' => 'mp3',
        'video/mp4' => 'mp4',



    ];

    const OctetStreamTypeExtension = [
        'rar' => 'application/x-rar',
        'zip' => 'application/zip',
        'mp3' => 'audio/mp3',
        'doc' => 'application/msword',
        'eps' => 'application/eps',
        'cdr' => 'application/cdr',
        'mac' => 'application/mac',
        'pptm' => 'application/pptm',
    ] ;
    /**
     * Default directory permissions (destination dir)
     */
    protected $createMini = false;
    protected $miniSize = 60;

    /**
     * Default directory persmissions (destination dir)
     */
    protected $default_permissions = 0775;


    /**
     * File post array
     *
     * @var array
     */
    protected $files_post = array();


    /**
     * Destination directory
     *
     * @var string
     */
    protected $destination;

    /**
     * @var bool strict from mime-types validation
     */
    protected  $strictMimeValidation = false;


    /**
     * File info
     *
     * @var object
     */
    protected $finfo;


    /**
     * Data about file
     *
     * @var array
     */
    public $file = array();

    /**
     * @var array
     */
    public $file_post;


    /**
     * Max. file size
     *
     * @var int
     */
    protected $max_file_size;


    /**
     * Allowed mime types
     *
     * @var array
     */
    protected $mimes = array();


    /**
     * External callback object
     *
     * @var object
     */
    protected $external_callback_object;


    /**
     * External callback methods
     *
     * @var array
     */
    protected $external_callback_methods = array();


    /**
     * Temp path
     *
     * @var string
     */
    protected $tmp_name;


    /**
     * Validation errors
     *
     * @var array
     */
    protected $validation_errors = array();


    /**
     * Filename (new)
     *
     * @var string
     */
    protected $filename;


    /**
     * Internal callbacks (fileSize check, mime, etc)
     *
     * @var array
     */
    private $callbacks = array();

    /**
     * Root dir
     *
     * @var string
     */
    protected $root;


    public function setStrictMimeValidation(){
        $this->strictMimeValidation = true;
    }
    /**
     * @return mixed
     */
    public function getDefaultPermissions()
    {
        return $this->default_permissions;
    }

    /**
     * @param mixed $default_permissions
     */
    public function setDefaultPermissions($default_permissions)
    {
        $this->default_permissions = $default_permissions;
    }

    /**
     * @param mixed $createMini
     * @return Upload
     */
    public function setCreateMini($createMini)
    {
        $this->createMini = $createMini;
        return $this;
    }

    /**
     * @param int $miniSize
     * @return Upload
     */
    public function setMiniSize(int $miniSize): Upload
    {
        $this->miniSize = $miniSize;
        return $this;
    }


    /**
     * Return upload object
     *
     * $destination		= 'path/to/your/file/destination/folder';
     *
     * @param string $destination
     * @param string|bool $root
     * @return Upload
     */
    public static function factory($destination, $root = false) {

        return new Upload($destination, $root);
    }


    /**
     *  Define ROOT constant and set & create destination path
     *
     * @param string $destination
     * @param string|bool $root
     * @throws \Exception
     */
    public function __construct($destination, $root = false) {

        if ($root) {
            $this->root = $root;
        }

        // set & create destination path
        if (!$this->set_destination($destination)) {

            throw new \Exception('Upload: Can\'t create destination. '.$this->root . $this->destination);

        }

        //create finfo object
        $this->finfo = new \finfo();

    }

    /**
     * Set target filename
     *
     * @param string $filename
     */
    public function set_filename($filename) {

        $this->filename = $filename;

    }

    /**
     * Check & Save file
     *
     * Return data about current upload
     *
     * @return array
     */
    public function upload() {
        if ($this->check()) {
            $this->save();
        }

        // return state data
        return $this->get_state();

    }


    /**
     * Save file on server
     *
     * Return state data
     *
     * @return array
     */
    public function save() {

        $this->save_file();

        return $this->get_state();

    }


    /**
     * Validate file (execute callbacks)
     *
     * Returns TRUE if validation successful
     *
     * @return bool
     */
    public function check() {

        //execute callbacks (check filesize, mime, also external callbacks
        $this->validate();

        //add error messages
        $this->file['errors'] = $this->get_errors();

        //change file validation status
        $this->file['status'] = empty($this->validation_errors);

        return $this->file['status'];

    }


    /**
     * Get current state data
     *
     * @return array
     */
    public function get_state() {

        return $this->file;

    }


    /**
     * Save file on server
     */
    protected function save_file()
    {

        $this->create_new_filename();

        //set filename
        $this->file['filename']	= $this->filename;

        //set full path
        $this->file['full_path'] = $this->root . $this->destination . $this->filename .'.'. $this->file['type'];
        $this->file['path'] = $this->destination . $this->filename . '.' . $this->file['type'];
        $status = move_uploaded_file($this->tmp_name, $this->file['full_path']);

        $imageTypes = [
            'jpg',
            'jpeg',
        ];

        if (in_array($this->file['type'], $imageTypes)) {
            $imgUtil = new ImageUtil($this->file['path']);
            $imgUtil->rotate();
        }

        if($this->createMini){
            $img = $this->file['path'];
            $this->file['path_mini'] = $this->destination . "mini_" . $this->filename . '.' . $this->file['type'];

            //Обрезаем картинку
            $imgUtil = new ImageUtil($img);
            $imgUtil->resizeDz($this->file['path_mini'], $this->miniSize, $this->miniSize);
        }
        //checks whether upload successful
        if (!$status) {
            throw new \Exception('Upload: Can\'t upload file.');
        }

        //done
        $this->file['status']	= true;

    }


    /**
     * Set data about file
     */
    protected function set_file_data() {

        $file_size = $this->get_file_size();
        $mime_type = $this->get_file_mime();
        $array = explode('.', $this->file_post['name']);
        $ext = array_pop($array);

        if (isset(self::OctetStreamTypeExtension[$ext]) && $this->file_post['type']==='application/octet-stream'){
            $mime_type = self::OctetStreamTypeExtension[$ext];
        }

        $this->file = array(
            'status'				=> false,
            'destination'			=> $this->destination,
            'size_in_bytes'			=> $file_size,
            'size_in_mb'			=> $this->bytes_to_mb($file_size),
            'mime'					=> $mime_type,
            'mime_type'				=> $this->get_file_mime(),
            'original_filename'		=> $this->file_post['name'],
            'tmp_name'				=> $this->file_post['tmp_name'],
            'post_data'				=> $this->file_post,
        );
        $this->file['type']	= $this->get_file_type();

    }

    /**
     * Set validation error
     *
     * @param string $message
     */
    public function set_error($message) {

        $this->validation_errors[] = $message;

    }


    /**
     * Return validation errors
     *
     * @return array
     */
    public function get_errors() {

        return $this->validation_errors;

    }


    /**
     * Set external callback methods
     *
     * @param object $instance_of_callback_object
     * @param array $callback_methods
     * @throws \Exception
     */
    public function callbacks($instance_of_callback_object, $callback_methods) {

        if (empty($instance_of_callback_object)) {

            throw new \Exception('Upload: $instance_of_callback_object can\'t be empty.');

        }

        if (!is_array($callback_methods)) {

            throw new \Exception('Upload: $callback_methods data type need to be array.');

        }

        $this->external_callback_object	 = $instance_of_callback_object;
        $this->external_callback_methods = $callback_methods;

    }


    /**
     * Execute callbacks
     */
    protected function validate() {

        //get curent errors
        $errors = $this->get_errors();

        if (empty($errors)) {

            //set data about current file
            $this->set_file_data();

            //execute internal callbacks
            $this->execute_callbacks($this->callbacks, $this);

            //execute external callbacks
            $this->execute_callbacks($this->external_callback_methods, $this->external_callback_object);

        }

    }


    /**
     * Execute callbacks
     * @param array $callbacks
     * @param object $object
     */
    protected function execute_callbacks($callbacks, $object) {

        foreach($callbacks as $method) {

            $object->$method($this);

        }

    }

    /**
     * @param object $object
     * @return object
     */
    protected function prepare_object(object $object): object
    {

        //наш uploader путает xlsx и xlsm, так что фиксим
        if ($object->file['mime'] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&  pathinfo($object->file_post['name'], PATHINFO_EXTENSION) === 'xlsm'){
            $object->file['mime'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
            $object->file['mime_type'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
            $object->file['type'] = 'xlsm';
        }

        if (($object->file['mime'] === 'image/jpeg' || $object->file['mime_type'] === 'image/jpeg') && in_array(pathinfo($object->file_post['name'], PATHINFO_EXTENSION), ['jpg', 'png'])){
            /** Как Выяснилось - очень частая проблема - jpg файлик думает, что он png или jpeg */
            $object->file_post['name'] = str_replace('.' . pathinfo($object->file_post['name'], PATHINFO_EXTENSION), '.jpg', $object->file_post['name']);
            $mimeType = pathinfo($object->file_post['name'], PATHINFO_EXTENSION) === 'jpg' ? 'image/jpg' : 'image/jpeg';
            $object->file_post['type'] = $mimeType;
            $object->file['mime'] = $mimeType;
            $object->file['mime_type'] = $mimeType;
            $object->file['type'] = pathinfo($object->file_post['name'], PATHINFO_EXTENSION);
        }

        return $object;
    }

    /**
     * File mime type validation callback
     * @param object $object
     */
    protected function check_mime_type($object)
    {
        $object = $this->prepare_object($object);

        if (!empty($object->mimes)) {
            if (!array_key_exists($object->file['mime'], $object->mimes) || $object->mimes[$object->file['mime']] !== pathinfo($object->file_post['name'], PATHINFO_EXTENSION)) {
                if ((array_key_exists($object->file['mime'], $object->mimes) && $object->mimes[$object->file['mime']] !== 'jpeg') || pathinfo($object->file_post['name'], PATHINFO_EXTENSION) !== 'jpg') {
                    //Меняем расширение подозрительным файлам
                    if ($object->strictMimeValidation === true) {
                        $object->set_error('Неправильный тип файла');
                    } else {
                        $object->file['type'] = 'ssp';
                        $object->file['mime'] = '';
                    }
                }
            }
        } else {
            if ($object->strictMimeValidation === true){
                $object->set_error('Неправильный тип файла');
            } else {
                $object->file['type'] = 'ssp';
                $object->file['mime'] = '';
            }
        }
    }

    /**
     * Проверка mime и extension
     * @param object $object
     */
    protected function check_mime_extension_type(object $object): void
    {
        $object = $this->prepare_object($object);

        if (!empty($object->mimes)) {
            if (!array_key_exists($object->file['mime'], $object->mimes) && $object->strictMimeValidation === true) {
                $object->set_error('Неправильный тип файла');
                return;
            }

            $extension = strtolower(pathinfo($object->file_post['name'], PATHINFO_EXTENSION));
            if (is_string($object->mimes[$object->file['mime']])) {
                if ($object->mimes[$object->file['mime']] === 'jpeg' && $extension === 'jpg') {
                    return;
                }
                if ($object->mimes[$object->file['mime']] !== $extension) {
                    $object->set_error('Неправильное расширение файла');
                    return;
                }
            }

            if (is_array($object->mimes[$object->file['mime']]) && !in_array($extension, $object->mimes[$object->file['mime']])) {

                $object->set_error('Неправильное расширение файла');
            }
        } else {
            if ($object->strictMimeValidation === true){
                $object->set_error('Неправильный тип файла');
            }
        }
    }

    /**
     * Set allowed mime types
     * @param array $mimes
     * @param string $callback
     * @param bool $onlyTransferredMimes
     */
    public function set_allowed_mime_types(array $mimes, string $callback = 'check_mime_type', bool $onlyTransferredMimes = false): void
    {
        if ($onlyTransferredMimes) {
            $this->mimes = $mimes;
        } else {
            $mimeTypes = [];
            foreach ($mimes as $mimeType){
                if (isset(self::AvailableMimeTypes[$mimeType])){
                    $mimeTypes[$mimeType] = self::AvailableMimeTypes[$mimeType];
                }
            }
            $this->mimes = $mimeTypes;
        }

        //if mime types is set -> set callback
        $this->callbacks[] = $callback;
    }


    /**
     * File size validation callback
     *
     * @param object $object
     */
    protected function check_file_size($object) {

        if (!empty($object->max_file_size)) {

            $file_size_in_mb = $this->bytes_to_mb($object->file['size_in_bytes']);

            if ($object->max_file_size <= $file_size_in_mb) {

                $object->set_error('Файл слишком большой.');

            }

        }

    }


    /**
     * Set max. file size
     *
     * @param int $size
     */
    public function set_max_file_size($size) {

        $this->max_file_size	= $size;

        //if max file size is set -> set callback
        $this->callbacks[]	= 'check_file_size';

    }


    /**
     * Set File array to object
     *
     * @param array $file
     */
    public function file($file) {
        $this->validation_errors = [];

        $this->set_file_array($file);

    }


    /**
     * Set file array
     *
     * @param array $file
     */
    protected function set_file_array($file) {

        //checks whether file array is valid
        if (!$this->check_file_array($file)) {

            //file not selected or some bigger problems (broken files array)
            $this->set_error('Проблема с загрузкой файла. Обратитесь в техподдержку');

        }

        //set file data
        $this->file_post = $file;

        //set tmp path
        $this->tmp_name  = $file['tmp_name'];

    }


    /**
     * Checks whether Files post array is valid
     *
     * @param array $file
     * @return bool
     */
    protected function check_file_array($file) {
        return isset($file['error'])
            && !empty($file['name'])
            && !empty($file['type'])
            && !empty($file['tmp_name'])
            && !empty($file['size']);

    }


    /**
     * Get file mime type
     *
     * @return string
     */
    protected function get_file_mime() {

        return $this->finfo->file($this->tmp_name, FILEINFO_MIME_TYPE);

    }

    /**
     * Get file  type
     * @return string
     */
    protected function get_file_type(): string
    {
        $fileType = 'ssp';
        if (isset($this->mimes[$this->file['mime']])) {
            if (is_string($this->mimes[$this->file['mime']]) ) {
                $fileType = $this->mimes[$this->file['mime']];
            } else if (is_array($this->mimes[$this->file['mime']]) && in_array(pathinfo($this->file_post['name'], PATHINFO_EXTENSION), $this->mimes[$this->file['mime']])) {
                $fileType = pathinfo($this->file_post['name'], PATHINFO_EXTENSION);
            }
        }

        return $fileType;
    }

    /**
     * Get file size
     *
     * @return int
     */
    protected function get_file_size() {

        return filesize($this->tmp_name);

    }


    /**
     * Set destination path (return TRUE on success)
     *
     * @param string $destination
     * @return bool
     */
    protected function set_destination($destination) {

        $this->destination = $destination . DIRECTORY_SEPARATOR;

        return $this->destination_exist() ? TRUE : $this->create_destination();

    }


    /**
     * Checks whether destination folder exists
     *
     * @return bool
     */
    protected function destination_exist() {

        return is_writable($this->root . $this->destination);

    }


    /**
     * Create path to destination
     * @return bool
     * @internal param string $dir
     */
    protected function create_destination() {

        Log::channel('uploadlog')->info('Данные.', ['root' => $this->root, 'destination' => $this->destination, 'default_permissions' => $this->default_permissions]);
        return mkdir($this->root . $this->destination, $this->default_permissions, true);

    }


    /**
     * Set unique filename
     */
    protected function create_new_filename() {

        $filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
        $this->set_filename($filename);

    }


    /**
     * Convert bytes to mb.
     *
     * @param int $bytes
     * @return int
     */
    protected function bytes_to_mb($bytes) {

        return round(($bytes / 1048576), 2);

    }


} // end of Upload
