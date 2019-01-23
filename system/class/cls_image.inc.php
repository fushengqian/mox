<?php
class IMAGE
{
    private $imagePath;//图片路径

    private $outputDir;//输出文件夹

    private $memoryImg;//内存图像

    public function __construct($imagePath, $outputDir = null)
    {
        $this->imagePath = $imagePath;

        $this->outputDir = $outputDir;

        $this->memoryImg = null;
    }

    /**
     * 显示内存中的图片
     * @param $image
     */
    public function showImage()
    {
        if ($this->memoryImg != null) {
            $info = getimagesize($this->imagePath);

            $type = image_type_to_extension($info[2], false);

            header('Content-type:' . $info['mime']);

            $funs = "image{$type}";

            $funs($this->memoryImg);

            imagedestroy($this->memoryImg);

            $this->memoryImg = null;
        }
    }

    /**
     * 将图片以文件形式保存
     * @param $image
     * @param $save_file_name
     */
    private function saveImage($image, $save_file_name = '')
    {
        $info = getimagesize($this->imagePath);

        $type = image_type_to_extension($info[2], false);

        $funs = "image{$type}";

        $file_name = md5($this->imagePath);
        if ($save_file_name) {
            $file_name = $save_file_name;
        }

        if (empty($this->outputDir)) {
            $funs($image, $file_name.'.' . $type);
        } else {
            $funs($image, $this->outputDir . $file_name . '.' . $type);
        }
    }

    /**
     * 压缩图片
     * @param $width 压缩后宽度
     * @param $height 压缩后高度
     * @param bool $output 是否输出文件
     * @return resource
     */
    public function compressImage($width, $height, $output = false, $file_name = '')
    {
        $image = null;

        $info = getimagesize($this->imagePath);

        $type = image_type_to_extension($info[2], false);

        $fun = "imagecreatefrom{$type}";

        $image = $fun($this->imagePath);

        $thumbnail = imagecreatetruecolor($width, $height);

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);

        imagedestroy($image);

        if ($output) {
            $this->saveImage($thumbnail, $file_name);
        }

        $this->memoryImg = $thumbnail;

        return $this;
    }

    /**
     * 为图像添加文字标记
     *
     * @param $content 文本内容
     * @param $size 字体大小
     * @param $font 字体样式
     * @param bool $output 是否输出文件
     * @return $this
     */
    public function addTextmark($content, $size, $font, $output = false)
    {
        $info = getimagesize($this->imagePath);

        $type = image_type_to_extension($info[2], false);

        $fun = "imagecreatefrom{$type}";

        $image = $fun($this->imagePath);

        $color = imagecolorallocatealpha($image, 0, 0, 0, 80);

        $posX = imagesx($image) - strlen($content) * $size / 2;

        $posY = imagesy($image) - $size / 1.5;

        imagettftext($image, $size, 0, $posX, $posY, $color, $font, $content);

        if ($output) {
            $this->saveImage($image);
        }

        $this->memoryImg = $image;

        return $this;

    }

    /**
     * 为图片添加水印
     *
     * @param $watermark 水印图片路径
     * @param $alpha 水印透明度(0-100)
     * @param bool $output 是否输出文件
     * @return $this
     */
    public function addWatermark($watermark, $alpha, $output = false)
    {
        $image_info = getimagesize($this->imagePath);

        $image_type = image_type_to_extension($image_info[2], false);

        $image_fun = "imagecreatefrom{$image_type}";

        $image = $image_fun($this->imagePath);

        $mark_info = getimagesize($watermark);

        $mark_type = image_type_to_extension($mark_info[2], false);

        $mark_fun = "imagecreatefrom{$mark_type}";

        $mark = $mark_fun($watermark);

        $posX = imagesx($image) - imagesx($mark);

        $posY = imagesy($image) - imagesy($mark);

        imagecopymerge($image, $mark, $posX, $posY, 0, 0, $mark_info[0], $mark_info[1], $alpha);

        if ($output) {
            $this->saveImage($image);
        }

        $this->memoryImg = $image;

        return $this;
    }
}