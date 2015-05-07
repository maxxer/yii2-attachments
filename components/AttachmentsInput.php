<?php

namespace nemmo\attachments\components;

use kartik\file\FileInput;
use nemmo\attachments\models\UploadForm;
use nemmo\attachments\ModuleTrait;
use yii\bootstrap\Widget;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 13.02.2015
 * Time: 21:18
 */
class AttachmentsInput extends Widget
{
    use ModuleTrait;

    public $id = 'file-input';

    public $model;

    public $pluginOptions = [];

    public $options = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        FileHelper::removeDirectory($this->getModule()->getUserDirPath()); // Delete all uploaded files in past

        $this->pluginOptions = array_replace($this->pluginOptions, [
            'uploadUrl' => Url::toRoute('/attachments/file/upload'),
            'initialPreview' => $this->model->isNewRecord ? [] : $this->model->getInitialPreview(),
            'initialPreviewConfig' => $this->model->isNewRecord ? [] : $this->model->getInitialPreviewConfig(),
            'uploadAsync' => false,
            'previewTemplates' => ['image' => '<div class="file-preview-frame" id="{previewId}" data-fileindex="{fileindex}">
    <img src="{data}" class="file-preview-image" title="{caption}" alt="{caption}" placeholder="Enter a caption" >
         {footer}
         
         <input class="your-form-class" type="text" name="imageTitle[]" id="imageTitle_{fileindex}">
</div>'],
            'uploadExtraData' => new \yii\web\JsExpression("function() {
    var obj = {};
    $('.your-form-class').each(function() {
        var id = $(this).attr('id'), val = $(this).val();
        obj[id] = val;
    });
    return obj;
}"),
        ]);

        $this->options = array_replace($this->options, [
            'id' => $this->id,
            //'multiple' => true
        ]);

        $js = <<<JS
var fileInput = $('#file-input');
var form = fileInput.closest('form');
var filesUploaded = false;
var filesToUpload = 0;
//var formSubmit = false;
form.on('beforeSubmit', function(event) { // form submit event
    console.log('submit');
    if (!filesUploaded && filesToUpload) {
        console.log('upload');
        $('#file-input').fileinput('upload').fileinput('lock');

        return false;
    }
});

fileInput.on('filebatchuploadcomplete', function(event, files, extra) { // all files successfully uploaded
    //var form = data.form;
    //console.log(form);
    console.log('uploaded');
    filesUploaded = true;
    $('#file-input').fileinput('unlock');
    form.submit();
});

fileInput.on('filebatchselected', function(event, files) { // there are some files to upload
    filesToUpload = files.length
});

fileInput.on('filecleared', function(event) { // no files to upload
    filesToUpload = 0;
});

JS;

        \Yii::$app->view->registerJs($js);
    }

    public function run()
    {
        $fileinput = FileInput::widget([
            'model' => new UploadForm(),
            'attribute' => 'file[]',
            'options' => $this->options,
            'pluginOptions' => $this->pluginOptions
        ]);

        return Html::tag('div', $fileinput, ['class' => 'form-group']);
    }
}