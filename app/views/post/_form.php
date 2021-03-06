<?php

use app\components\Helpers;
use app\components\widgets\FileUploadFormWidget;
use app\components\widgets\PostCardImageWidget;
use app\components\widgets\SketchWidget;
use dosamigos\ckeditor\CKEditor;
use kartik\widgets\ColorInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form yii\widgets\ActiveForm */

/*
<select>
    <option style="font-family : Arial">Arial</option>
    <option style="font-family : Courier">Courier</option>
    <option style="font-family : Tahoma">Tahoma</option>
    <option style="font-family : 'Times New Roman'">Times New Roman</option>
    <option style="font-family : Verdana">Verdana</option>
    <option style="font-family : 'Comic Sans MS'">Comic Sans</option>
</select>
*/

?>


<div class="post-form">

    <?php
        $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableClientValidation' => true,
        ]);

    $js = <<<JS
        // get the form id and set the event
        $('form#{$model->formName()}').on('afterValidate', function(event, attribute, messages, deferreds) {

            console.log(event);
            console.log(attribute);
            console.log(messages);
            console.log(deferreds);

            var form = $(this);
            if(form.find('.has-error').length) {
                console.log('tiene error...');
                closeLoading();
                    return false;
            }else{
                openLoading();
                console.log('TODO BIEN');
                    return true;
            }

        }).on('submit', function(e){


            /*
               return false;

             var form = $(this);
             if(form.find('.has-error').length) {

                    closeLoading();
                        return false;

                }else{

                     // openLoading();

                     if(form.find('.has-error').length) {
                        closeLoading();
                         return false;
                     }

                    return true;
                }
                */


        });
JS;

    $this->registerJs($js);

    ?>

    <?php

        /*
         * All tema de las postcards
         * */
        echo PostCardImageWidget::widget([
            'model' => $model,
            'hiddenField' => 'imagen_portada',
        ]);
    ?>

    <?php
        if($model->color=='') $model->color = 'white';
        echo $form->field($model, 'color')->widget(ColorInput::classname(), [
            // 'value' => '#fff',//($model->color!='') ? $model->color : '#fff',
            'options' => [
                'placeholder' => 'Select color ...',
            ],
            'pluginOptions' => [
                'showInput' => true,
                'showInitial' => true,
                'showPalette' => true,
                'showPaletteOnly' => true,
                'showSelectionPalette' => true,
                'showAlpha' => false,
                'allowEmpty' => false,
                'preferredFormat' => 'name',
                'palette' =>  Helpers::getPaletteColors(),
            ]
        ]);
    ?>

    <?= $form->field($model, 'nombre_persona')->textInput(['maxlength' => true]) ?>

    <?php

    /*
        $title_maxlenght = $model->getAttributeRule('title','maxlenght');
        echo $form->field($model, 'title')->widget(CKEditor::className(), [
            'options' => [
                'rows' => 1,
                'maxlength' => $title_maxlenght,
            ],
            'preset' => 'advance',
        ]);
    */

    echo $form->field($model, 'title')->textInput(['maxlength' => true]);


    $fontData = [
        'Arial' => 'Arial',
        'Courier' => 'Courier',
        'Tahoma' => 'Tahoma',
        '\'Times New Roman\'' => 'Times New Roman',
        'Verdana' => 'Verdana',
        '\'Comic Sans MS\'' => 'Comic Sans MS',
        '\'Helvetica Neue\'' => 'Helvetica Neue',
        'sans-serif' => 'sans-serif',
        'Lucida Grande' => 'Lucida Grande',
    ];

    // dar formato al select2 : http://demos.krajee.com/widget-details/select2
    $format = <<< SCRIPT
        function format(font) {
            return '<span style="font-family: '+font.id+'">' + font.text + '</span>';
        }
SCRIPT;

    $escape = new JsExpression("function(m) { return m; }");
    $this->registerJs($format, View::POS_HEAD);

        echo $form->field($model, 'font')->widget(Select2::classname(), [
            'data' => $fontData,
            'language' => Yii::$app->language,
            'options' => [
                'encodeLabels' => false,
                'placeholder' => $model->getAttributeLabel('font'),
                'maxlenght' => true,
            ],
            'pluginOptions' => [
                'templateResult' => new JsExpression('format'),
                'templateSelection' => new JsExpression('format'),
                'escapeMarkup' => $escape,
                'allowClear' => true
            ],
        ]);

    ?>

    <?= $form->field($model, 'que_es')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'consejo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
    ]) ?>

    <?php


        if($model->isNewRecord){
            $model->lang = Yii::$app->language;
        }
        echo $form->field($model, 'lang')->label(false)->hiddenInput(['value'=>$model->lang]);

    ?>

    <?php
        if($model->isNewRecord){
            $model->publico = 1;
        }
        echo $form->field($model, 'publico')->widget(SwitchInput::classname(), [
            'pluginOptions' => [
                // 'size' => 'large',
                'onColor' => 'success',
                'offColor' => 'danger',
                'onText' => Yii::t('app','yes'),
                'offText' => Yii::t('app','No'),
            ]
        ]);
    ?>

    <?php
        /*
         * Sketch
         * De momento na
         * */
        $enableCanvas = false;
        if($enableCanvas){
            echo $model->getAttributeLabel('canvas');
            echo SketchWidget::Widget();
            echo $form->field($model,'canvas')->label(false)->hiddenInput();
        }
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success','style'=>'width:100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
        if(!$model->isNewRecord){
            echo FileUploadFormWidget::widget(['modelPadre' => $model]);
        }else{
            echo '<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;'.Yii::t('app','post.info.pic').'</div>';
        }
    ?>

</div>
