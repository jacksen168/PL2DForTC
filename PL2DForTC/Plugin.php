<?php

/**
 * 无法启用时检查该插件文件夹名是否为"PL2DForTC"<br>
 * PL2DForTC: <br>
 * 一款扩展性无可挑剔的 Live2D 插件<br>
 * 全名 PIXI Live2D display for Typecho<br>
 * 支持全部版本模型的Live2D插件<br>
 * 插件引用<a href="https://github.com/guansss/pixi-live2d-display">@guansss</a> github的PIXI_Live2D_display.js项目进行开发<br>
 *
 * @package PL2DForTC
 * @author jacksen168
 * @version 0.6.1
 * @link https://www.jacksen168.top/
 */

global $package, $version;
$package = 'PL2DForTC';
$version = '0.6.1';

class PL2DForTC_Plugin implements Typecho_Plugin_Interface
{

    /* 激活插件方法 */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('PL2DForTC_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('PL2DForTC_Plugin', 'footer');
    }

    /* 禁用插件方法 */
    public static function deactivate()
    {
    }

    /* 插件配置方法 */
    public static function config(Typecho_Widget_Helper_Form $form)
    {

        // 插件信息与更新检测
        function paul_update($name, $version)
        {
            echo "<div class='PL-info'>";
            echo "<h2>PL2DForTC Live2D看板娘插件 (当前版本: " . $version . ")</h2>";
            echo "<p>By: <a href='https://github.com/jacksen168'>jacksen168</a></p>";
            echo "<p class='buttons'><a href='https://www.jacksen168.top/index.php/archives/PL2DForTC.html'>项目介绍</a>
                  <a href='https://github.com/jacksen168/PL2DForTC/releases'>更新日志</a></p>";

            $update = @file_get_contents("https://www.jacksen168.top/api/update/?name=" . $name . "&current=" . $version . "&website=" . $_SERVER['HTTP_HOST']);
            $httpStatusCode = substr($http_response_header[0], 9, 3); // 获取HTTP状态码

            if ($httpStatusCode === '200') {
                $update = json_decode($update, true);

                if (isset($update['text'])) {
                    echo '<p class="PL-info-text">' . $update['text'] . '</p>';
                };

                if (isset($update['message'])) {
                    echo '<p class="PL-info-message">' . $update['message'] . '</p>';
                };

                if (!isset($update['text']) && !isset($update['message'])) {
                    echo $update;
                }
            } else {
                // 处理HTTP错误
                echo '<p class="PL-info-text">检查更新错误</p>';
                // 输出HTTP状态码和错误消息
                echo '<p class="PL-info-text">HTTP状态码: ' . $httpStatusCode . '</p>';
                echo '<p class="PL-info-text">错误消息: ' . $http_response_header[0] . '</p>';
            }

            echo "</div>";
        }

        global $package, $version;
        paul_update($package, $version);


        $form->setAttribute('class', 'P-main-content');

        function configuration()
        {
            global $package, $version;
            $plugins_url = Helper::options()->pluginUrl;
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo '<script src="' . $plugins_url . '/PL2DForTC/js/PL2DForTC.js"></script>';
            echo '<script src="' . $plugins_url . '/PL2DForTC/js/PL2DForTC.log.js"></script>';
            echo '<link href="' . $plugins_url . '/PL2DForTC/css/TL2DForTC.css" rel="stylesheet">';
            echo '<script> let plugins_url = "' . $plugins_url . '";</script>';
            echo '<script>var project = { "name":"' . $package . '","version":"' . $version . '" };print_log(project);</script>';
        }
        configuration();
        // 例遍文件函数
        function read_files_folder($dir_path, $original_dir_path)
        {
            $data_array = array();
            $files = scandir($dir_path);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $file_path = $dir_path . '/' . $file;
                if (is_file($file_path)) {
                    $data_array[] = array(
                        'name' => $file,
                        'type' => 'file',
                        'url' => str_replace('../usr/plugins/PL2DForTC/models/', '', $dir_path) . '/' . $file
                    );
                } elseif (is_dir($file_path)) {
                    if ($dir_path == $original_dir_path) {
                        # 目录文件夹
                        $data_array_url = $file;
                    } else {
                        # 非目录文件夹
                        $data_array_url = str_replace('../usr/plugins/PL2DForTC/models/', '', $dir_path) . '/' . $file;
                    };

                    $data_array[] = array(
                        'name' => $file,
                        'type' => 'folder',
                        'url' => $data_array_url,
                        'files' => read_files_folder($file_path, '')
                    );
                }
            }

            return $data_array;
        }

        $dir_path = '../usr/plugins/PL2DForTC/models';
        $data_array = read_files_folder($dir_path, $dir_path);

        // 目录树渲染函数
        function render_array($arr)
        {
            $html = '<ul>';

            foreach ($arr as $file) {
                $type = $file['type'];
                $name = $file['name'];
                $url = $file['url'];
                $input_name = 'folder-' . md5($url);

                if ($type == 'file') {
                    $extension = pathinfo($name, PATHINFO_EXTENSION);

                    $photo_extension = array('jpg', 'jpeg', 'png', 'gif');  //图片
                    $compress_extensions = array('zip', 'rar', '7z', 'gz', 'tar', 'bz2', 'xz'); //压缩文件
                    $audio_extensions = array('mp3', 'wav', 'aiff', 'midi', 'aac', 'wma', 'm4a', 'flac', 'ogg',);   //音频文件
                    $video_extensions = array('mp4', 'mov', 'avi', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp', 'mpg', 'mpeg');    //视频文件

                    if (in_array($extension, $photo_extension)) {
                        $file_icon = 'icon-image-full';
                    } elseif ($extension == 'json') {
                        $file_icon = 'icon-json-full';
                    } elseif (in_array($extension, $compress_extensions)) {
                        $file_icon = 'icon-zip-full';
                    } elseif (in_array($extension, $audio_extensions)) {
                        $file_icon = 'icon-audio-full';
                    } elseif (in_array($extension, $video_extensions)) {
                        $file_icon = 'icon-avi-full';
                    } else {
                        $file_icon = 'icon-file-full';
                    }

                    $onclick_url = Helper::options()->pluginUrl . '/PL2DForTC/models/';

                    $html .= '<li class="file iconfont icon ' . $file_icon . '">';
                    if ($extension == 'json') {
                        $html .= '<a herf="javascript:;" onclick="detection_files(2,\'' . $onclick_url . $url . '\')">' . $name . '</a>';
                    } else {
                        $html .= '<a herf="javascript:;">' . $name . '</a>';
                    }
                    $html .= '</li>';
                } elseif ($type == 'folder' && count($file['files']) > 0) {
                    $html .= '<li class="folder iconfont icon">';
                    $html .= '<input type="checkbox" name="' . $input_name . '">';
                    $html .= '<label class="iconfont" for="' . $input_name . '" onclick="toggleFolder(this)">' . $name . '</label>';
                    $html .= render_array($file['files']);
                    $html .= '</li>';
                } elseif ($type == 'folder iconfont icon') {
                    $html .= '<li class="folder">';
                    $html .= '<input type="checkbox" name="' . $input_name . '">';
                    $html .= '<label class="iconfont" for="' . $input_name . '" onclick="toggleFolder(this)">' . $name . '</label>';
                    $html .= '</li>';
                }
            }

            if (!$arr) {
                $html .= '没有.json文件';
            }

            $html .= '</ul>';

            return $html;
        }

        // 获取json文件url[模型选择数组]
        function getJsonUrls($data_array, $urls = [])
        {
            foreach ($data_array as $item) {
                if (isset($item['name']) && pathinfo($item['name'], PATHINFO_EXTENSION) === 'json') {
                    $urls[] = $item['url'];
                }
                if (isset($item['files'])) {
                    $urls = getJsonUrls($item['files'], $urls);
                }
            }
            return $urls;
        }

        $jsonUrls_array = getJsonUrls($data_array);
        $jsonUrls_json = json_encode($jsonUrls_array);

        //获取模型配置文件url
        function getModelsJosnUrl($json_path, $dir_path)
        {
            $model_json_path = array();
            foreach ($json_path as $file_path) {
                $json_file = $dir_path . '/' . $file_path;
                $json_string = file_get_contents($json_file);
                $json_data = json_decode($json_string, true);
                if (isset($json_data['FileReferences']) || isset($json_data['fileReferences']) || isset($json_data['textures']) || isset($json_data['Textures'])) {
                    $model_json_path[] = $file_path;
                }
            }
            return $model_json_path;
        }

        // print_r(getModelsJosnUrl($jsonUrls_array, $dir_path));
        $modelJsonUrl_array = getModelsJosnUrl($jsonUrls_array, $dir_path);
        $modelJsonUrl_json = json_encode($modelJsonUrl_array);

        global $package, $version;
        echo '<div class="P-main-content">
        <div class="P-navigation-bar">
        <div style="text-align: center;"><h3>' . $package . ' ' . $version . '</h3></div>
        <ul class="P-tab">
        <li class="P-tab-bar active" tabindex="1" onclick="nav_bar(event,\'nav-bar-0\')">模型目录</li>
        <li class="P-tab-bar" tabindex="1" onclick="nav_bar(event,\'nav-bar-1\')">模型选择</li>
        <li class="P-tab-bar" tabindex="1" onclick="nav_bar(event,\'nav-bar-2\')">页面设置</li>
        <li class="P-tab-bar" tabindex="1" onclick="nav_bar(event,\'nav-bar-3\')">画布设置</li>
        <li class="P-tab-bar" tabindex="1" onclick="nav_bar(event,\'nav-bar-4\')">模型设置</li>
        <li class="P-tab-bar" tabindex="1" onclick="nav_bar(event,\'nav-bar-5\')">其他设置</li>
        </ul>
        <button class="btn" type="button" onclick="detection_files(1)">检查并解锁保存按钮</button>
        </div>
        </div>';



        //目录树渲染

        echo '<div class="PL-main-content-tree P-main-content nav-bar-0"><div class="tree-box">';
        echo '<h2 style="text-align: center;">models目录</h2>';
        echo '<div class="tree">';
        echo render_array($data_array);
        echo '</div></div>';
        echo '<script>function toggleFolder(label) {
            let li = label.parentNode;
            li.classList.toggle("open");}</script>';


        // 选择模型
        $choose_models_div = '<div class="tab-container">
        <div class="tab-content">
            <div class="tab active" onclick="openTab(event, \'choose_models_tab1\')">智能筛选</div>
            <div class="tab" onclick="openTab(event, \'choose_models_tab2\')">未经筛选</div>
        </div>

        <div class="tab-main">
            <div id="choose_models_tab1" class="tab-main-content active"></div>
            <div id="choose_models_tab2" class="tab-main-content"></div>
        </div></div><script>show_choose_button(\'' . $modelJsonUrl_json . '\',\'' . $jsonUrls_json . '\')</script>
        模型请放在models目录下,模型配置文件通常为<a>.json</a>后缀的文本文件,[选择外链模型]为空时采用;<br>
        智能筛选: 已为你智能筛选出来的模型配置文件,<br>
        未经筛选: 所有<a>.json</a>文件;<br>
        如果你正确的模型配置文件没有出现在智能筛选里面,可反馈给我。';

        $choose_models = new Typecho_Widget_Helper_Form_Element_Text(
            'choose_models',
            NULL,
            'HK416_805/normal.model3.json',
            _t('选择模型'),
            _t($choose_models_div)
        );
        $choose_models->setAttribute('id', 'choose_models');
        $choose_models->setAttribute('class', 'P-ul P-ul-text nav-bar-1');
        $form->addInput($choose_models);

        // 选择外链模型
        $custom_model = new Typecho_Widget_Helper_Form_Element_Text('custom_model', NULL, NULL, _t('选择外链模型'), _t('在这里填入一个模型配置文件 <a>json</a> 文件的地址，可供使用外链模型，不填则使用插件目录下的模型'));
        $custom_model->setAttribute('id', 'custom_model');
        $custom_model->setAttribute('class', 'P-ul P-ul-text nav-bar-1');
        $form->addInput($custom_model);

        $transparent = new Typecho_Widget_Helper_Form_Element_Radio('transparent', array(
            false => '开启调试(黑色背景显示)',
            true => '关闭调试(黑色背景隐藏)'
        ), true, '开启调试模式', '是否开启调试模式？开启后 背景/画布 将显示为黑色,便于模型的调试');
        $transparent->setAttribute('class', 'P-ul P-ul-radio nav-bar-2');
        $form->addInput($transparent);


        // 自定义定位
        $position = new Typecho_Widget_Helper_Form_Element_Radio(
            'position',
            array(
                'left' => _t('靠左'),
                'right' => _t('靠右'),
            ),
            'left',
            _t('自定义位置'),
            _t('自定义看板娘所在的位置')
        );
        $position->setAttribute('class', 'P-ul P-ul-radio nav-bar-2');
        $form->addInput($position);

        //画布遮挡
        $pointer_events = new Typecho_Widget_Helper_Form_Element_Radio('pointer_events', array(
            'unset' => '开启(实化)',
            'none' => '关闭(虚化)'
        ), 'none', '开启模型交互(画布虚化/实化)/与模型互动', '开启后画布将实化(鼠标点击得到画布,也会挡住画布后的内容)');
        $pointer_events->setAttribute('class', 'P-ul P-ul-radio nav-bar-2');
        $form->addInput($pointer_events);

        //小屏隐藏模型
        $small_screen_num = new Typecho_Widget_Helper_Form_Element_Text('small_screen_num', NULL, 768, '小屏幕(手机)模型隐藏开启条件', '默认为768,单位 px');
        $small_screen_num->setAttribute('class', 'P-ul P-ul-text nav-bar-2');
        $form->addInput($small_screen_num);

        $small_screen = new Typecho_Widget_Helper_Form_Element_Radio('small_screen', array(
            true => '开启(小屏隐藏)',
            false => '关闭(小屏不隐藏)'
        ), false, '开启小屏幕(手机)模型隐藏', '是否开启小屏幕(手机)模型隐藏? 开启后屏幕宽度小于 开启条件数值(px) 将隐藏模型');
        $small_screen->setAttribute('class', 'P-ul P-ul-radio nav-bar-2');
        $form->addInput($small_screen);

        $canvas_CSS_def = '
@media screen and (max-width: 1920px){ #PL2DForTC{ width: 20em; }
@media screen and (max-width: 1820px){ #PL2DForTC{ width: 17em; }
@media screen and (max-width: 1720px){ #PL2DForTC{ width: 15em; }
@media screen and (max-width: 1620px){ #PL2DForTC{ width: 13em; }
@media screen and (max-width: 1520px){ #PL2DForTC{ width: 10em; }
@media screen and (max-width: 768px){ #PL2DForTC{ width: 7em; }';

        //画布z-index
        $canvas_CSS = new Typecho_Widget_Helper_Form_Element_Textarea(
            'canvas_CSS',
            NULL,
            $canvas_CSS_def,
            '画布CSS样式',
            '默认: <div style="color:#fff;background:#383d45;padding: 5px;">
        @media screen and (max-width: 1920px){ #PL2DForTC{ width: 20em; }<br>
        @media screen and (max-width: 1820px){ #PL2DForTC{ width: 17em; }<br>
        @media screen and (max-width: 1720px){ #PL2DForTC{ width: 15em; }<br>
        @media screen and (max-width: 1620px){ #PL2DForTC{ width: 13em; }<br>
        @media screen and (max-width: 1520px){ #PL2DForTC{ width: 10em; }<br>
        @media screen and (max-width: 768px){ #PL2DForTC{ width: 7em; }<br>
        </div>画布非分辨率:缩放(直接放大,分辨率不变)<br>请用#PL2DForTC{ width: XXXem; }缩放,不懂勿改'
        );
        $canvas_CSS->setAttribute('class', 'P-ul P-ul-text nav-bar-3');
        $form->addInput($canvas_CSS);

        //画布z-index
        $canvas_Z_index = new Typecho_Widget_Helper_Form_Element_Text('canvas_Z_index', NULL, 520, '画布层级(z-index)', '默认为520,如果 画布/模型 被其他元素遮住数字往上加');
        $canvas_Z_index->setAttribute('class', 'P-ul P-ul-text nav-bar-3');
        $form->addInput($canvas_Z_index);

        //自定义像素密度
        $resolution = new Typecho_Widget_Helper_Form_Element_Text('resolution', NULL, 1, '画布缩放', '默认为1,非必要1就行了,渲染分辨率缩放(分辨率:缩放,消耗GPU)<br>等比例缩放画布宽度和高度');
        $resolution->setAttribute('class', 'P-ul P-ul-text nav-bar-3');
        $form->addInput($resolution);

        //自定义画布大小
        //-宽度
        $canvas_width = new Typecho_Widget_Helper_Form_Element_Text('canvas_width', NULL, 400, '画布宽度', '默认为400,按个人需求调整,渲染分辨率宽度(分辨率:宽度,消耗GPU)');
        $canvas_width->setAttribute('class', 'P-ul P-ul-text nav-bar-3');
        $form->addInput($canvas_width);
        //-高度
        $canvas_heigth = new Typecho_Widget_Helper_Form_Element_Text('canvas_heigth', NULL, 500, '画布高度', '默认为500,按个人需求调整,渲染分辨率高度(分辨率:高度,消耗GPU)');
        $canvas_heigth->setAttribute('class', 'P-ul P-ul-text nav-bar-3');
        $form->addInput($canvas_heigth);

        //自定义模型动作触发
        $HitAreasMotion = new Typecho_Widget_Helper_Form_Element_Textarea(
            'HitAreasMotion',
            null,
            'model_PIXI.on("hit", (hitAreas) => {
                if (!hitAreas.includes("")) {//点击任意区域触发
                    model_PIXI.motion("Tap");//触发TAP动作
                }
            });',
            '点击指定区域触发动作配置',
            '说明：触发动作需模型本身有动作预设,设置因模型而异,<br> 默认: <br><div style="color:#fff;background:#383d45;padding: 5px;">model_PIXI.on("hit", (hitAreas) => {if (!hitAreas.includes("")) {model_PIXI.motion("Tap");}});<br></div><a href="https://github.com/guansss/pixi-live2d-display">文案查看githb</a> | <a href="https://www.bilibili.com/video/BV1Jp4y1W7s3">B站UP主说明视频</a>'
        );
        $HitAreasMotion->setAttribute('class', 'P-ul P-ul-text nav-bar-5');
        $form->addInput($HitAreasMotion);

        //自定义代码
        $PIXI_code = new Typecho_Widget_Helper_Form_Element_Textarea(
            'PIXI_code',
            null,
            '',
            '此处可扩展代码到script标签中',
            '可扩展你的代码<a href="https://github.com/guansss/pixi-live2d-display">文案查看githb</a> | <a href="https://www.bilibili.com/video/BV1Jp4y1W7s3">B站UP主说明视频</a> | <a href="https://pixijs.com/">PIXIJS文案查看</a>'
        );
        $PIXI_code->setAttribute('class', 'P-ul P-ul-text nav-bar-5');
        $form->addInput($PIXI_code);

        //精灵尺寸/缩放
        $model_set = new Typecho_Widget_Helper_Form_Element_Text('model_set', NULL, 0.1, '模型缩放', '默认为0.1,按个人需求调整,说明:模型在画布中的大小缩放<br>等比例缩放模型宽度高度');
        $model_set->setAttribute('class', 'P-ul P-ul-text nav-bar-4');
        $form->addInput($model_set);

        //精灵位置X
        $model_X = new Typecho_Widget_Helper_Form_Element_Text('model_X', NULL, 0, '模型位置(轴X)', '默认为0,可为负数,按个人需求调整<br>说明:模型在画布中的位置');
        $model_X->setAttribute('class', 'P-ul P-ul-text nav-bar-4');
        $form->addInput($model_X);

        //精灵位置Y
        $model_Y = new Typecho_Widget_Helper_Form_Element_Text('model_Y', NULL, 0, '模型位置(轴Y)', '默认为0,可为负数,按个人需求调整<br>说明:模型在画布中的位置');
        $model_Y->setAttribute('class', 'P-ul P-ul-text nav-bar-4');
        $form->addInput($model_Y);

        //精灵宽度
        $model_width = new Typecho_Widget_Helper_Form_Element_Text('model_width', NULL, NULL, '模型宽度', '默认为 空/0,不填按模型宽度渲染<br>需同时填写模型高度时启用');
        $model_width->setAttribute('class', 'P-ul P-ul-text nav-bar-4');
        $form->addInput($model_width);

        //精灵高度
        $model_heigth = new Typecho_Widget_Helper_Form_Element_Text('model_heigth', NULL, NULL, '模型高度', '默认为 空/0,,不填按模型高度渲染<br>需同时填写模型宽度时启用');
        $model_heigth->setAttribute('class', 'P-ul P-ul-text nav-bar-4');
        $form->addInput($model_heigth);
    }


    /* 个人用户的配置方法 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /* 插件实现方法 */
    public static function header()
    {
        // 头
        $CSS_hear = '';
        if (Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->canvas_CSS) {
            $CSS_hear = Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->canvas_CSS;
        } else {
            $CSS_hear = '@media screen and (max-width: 1920px){ #PL2DForTC{ width: 20em; }@media screen and (max-width: 1820px){ #PL2DForTC{ width: 17em; }@media screen and (max-width: 1720px){ #PL2DForTC{ width: 15em; }@media screen and (max-width: 1620px){ #PL2DForTC{ width: 13em; }@media screen and (max-width: 1520px){ #PL2DForTC{ width: 10em; }@media screen and (max-width: 768px){ #PL2DForTC{ width: 7em; }';
        };

        $small_screen_incss = '';
        if (Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->small_screen == true) {
            if (Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->small_screen_num) {
                $small_screen_incss = '@media screen and (max-width: ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->small_screen_num . 'px){ #PL2DForTC{ display:none; }';
            } else {
                $small_screen_incss = '@media screen and (max-width: 768px){ #PL2DForTC{ display:none; }';
            }
        };

        echo '<style>
        #PL2DForTC{ left: 0;vertical-align: middle; z-index: ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->canvas_Z_index . ';}'
            . $CSS_hear
            . '</style>';

        echo '<style>.PL2DForTC-div {pointer-events: ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->pointer_events . '; position: fixed;bottom: 0;} .PL2DForTC-div.right {right:0;} .PL2DForTC-div.left {left:0;}</style>';
        echo '<style>' . $small_screen_incss . '</style>';
    }
    public static function footer()
    {
        //尾

        global $package, $version;
        $ppd = Helper::options()->pluginUrl;

        echo '<div id="PL2DForTC-div" class="PL2DForTC-div ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->position . '"><canvas id="PL2DForTC"></canvas></div>';
        echo "<script src='" . $ppd . "/PL2DForTC/js/live2dcubismcore.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/live2d.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/pixi.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/guansss.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/PL2DForTC.log.js'></script>" . "\n";
        echo '<script>var project = { "name":"' . $package . '","version":"' . $version . '" };print_log(project);</script>';

        $model_URL = $ppd . '/PL2DForTC/models';

        if (Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->custom_model) {
            echo '<script> const Model ="' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->custom_model . '";</script>' . "\n";
        } else {
            echo '<script> const Model ="' . $model_URL . '/' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->choose_models . '";</script>' . "\n";
        };

        if (Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_width && Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_heigth) {
            $model_wh = 'model_PIXI.width = ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_width . ';//精灵宽度
            model_PIXI.heigth = ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_heigth . ';//精灵高度';
        } else {
            $model_wh = '';
        }

        echo '<script>' .
            '(async function main() {

            const app = new PIXI.Application({
                view: document.getElementById("PL2DForTC"),
                autoStart: true,
                antialias: true,
                transparent: ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->transparent . ',
                resolution: ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->resolution . ', 
                forceCanvas: true,
            });
        
            app.renderer.autoResize = true;
            app.renderer.resize(' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->canvas_width . ', ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->canvas_heigth . ');//舞台宽度,高度
        
            const model_PIXI = await PIXI.live2d.Live2DModel.from(Model);

            ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->HitAreasMotion . '
            ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->PIXI_code . '
        
        
            /*--------配置示例
            model_PIXI.on("hit", (hitAreas) => {
                if (hitAreas.includes("Head")) {//点击Head区域触发
                    model_PIXI.motion("TapHead");//触发TapHead动作
                }
            });
            */
            
            app.stage.addChild(model_PIXI);
        
            model_PIXI.scale.set(' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_set . ');//模型缩放
        
            model_PIXI.x = ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_X . ';//X轴位置
            model_PIXI.y = ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->model_Y . ';//Y轴位置
            ' . $model_wh . '
        
        })();' .

            '</script>' . "\n";
    }
}
