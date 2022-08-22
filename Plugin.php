<?php

/**
 * PL2DForTC：<br>
 * 无法启用时检查该插件文件夹名是否为: PL2DForTC<br>
 * 一个扩展性无可挑剔的 Live2D 插件<br>
 * 全名 PIXI Live2D display for Typecho<br>
 * 支持全部版本模型的Live2D插件<br>
 * 插件引用<a href="https://github.com/guansss/pixi-live2d-display">@guansss</a> github的PIXI_Live2D_display.js项目进行开发<br>
 *
 * @package PL2DForTC
 * @author jacksen168
 * @version 0.5
 * @link https://www.jacksen168.top/
 */

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
            echo "<h2>PL2DForTC Live2D看板娘插件 (版本: " . $version . ")</h2>";
            echo "<p>By: <a href='https://github.com/jacksen168'>jacksen168</a></p>";
            echo "<p class='buttons'><a href='https://www.jacksen168.top/index.php/archives/PL2DForTC.html'>项目介绍</a>
                  <a href='https://github.com/jacksen168/PL2DForTC/releases'>更新日志</a></p>";

            $update = file_get_contents("https://www.jacksen168.top/api/update/?name=" . $name . "&current=" . $version . "&site=" . $_SERVER['HTTP_HOST']);
            $update = json_decode($update, true);

            if (isset($update['text'])) {
                echo '<p class="PL-info-text">' . $update['text'] . '</p>';
            };
            if (isset($update['message'])) {
                echo '<p class="PL-info-message">' . $update['message'] . '</p>';
            };

            echo "</div>";
        }
        paul_update("PL2DForTC", "0.5");


        $form->setAttribute('class', 'P-main-content');

        function configuration()
        {
            $plugins_url = Helper::options()->pluginUrl;
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            // echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rTTiRUKnSWaDu2FjhzWFl8/JuUZMlplyWE/djenb2LoKqkgLGfEGfSrL7XDLoB1M" crossorigin="anonymous"><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-Nj1D6pu2WnJojj+67GiU9ZFNwbl7bUWX5Kj5MS22C8bGjllemM9pvQyvj14zJb58" crossorigin="anonymous"></script>';
            echo '<script src="' . $plugins_url . '/PL2DForTC/js/PL2DForTC.js"></script>';
            echo '<link href="' . $plugins_url . '/PL2DForTC/css/TL2DForTC.css" rel="stylesheet">';
            echo '<script> let plugins_url = "' . $plugins_url . '"</script>';
        }
        configuration();

        //------------------------------------------------------读取深度为1的所有json文件--jacksen168原创------------------------

        function get_json_files($get_josn_files_type)
        {
            // 读取模型文件夹
            $models_for_get_json_files = array();
            $models_names = array();
            //读取models下所有文件夹--重组用
            foreach (glob("../usr/plugins/PL2DForTC/models/*") as $key => $value) {
                $models_for_get_json_files[substr($value, 32)] = substr($value, 32);
                $models_names[$key] = substr($value, 32);
            };
            //读取models下所有文件夹--检测用
            $models_folders = array();
            foreach (glob("../usr/plugins/PL2DForTC/models/*") as $key1 => $value) {
                $models_folders[$key1] = substr($value, 32);
            };
            $models_folder_files = array();
            //读取 模型文件夹 下 所有 模型文件夹下的文件 ，深度 = 1
            for ($i = 0; $i < count($models_folders); $i++) {
                $models_folder = array();
                foreach (glob("../usr/plugins/PL2DForTC/models/" . $models_folders[$i] . "/*") as $c => $value) {
                    $models_folder[$c] = substr($value, strlen("../usr/plugins/PL2DForTC/models/" . $models_folders[$i] . "/"));
                };
                $models_folder_files[$i] = $models_folder; //数组=models文件夹*n--->单个模型文件夹*n--->文件*n
            };
            $json_files_after_combined_with_folders = array();
            //筛除非json文件--模型文件夹
            for ($f = 0; $f < count($models_folder_files); $f++) { //根据models文件夹数组长度循环
                $json_files_after_combined_with_folders[$f] = "";
                $L = 0;
                $json_files = array();
                //筛除非json文件
                for ($j = 0; $j < count($models_folder_files[$f]); $j++) {
                    if (substr($models_folder_files[$f][$j], strpos($models_folder_files[$f][$j], '.json')) === '.json') {
                        //判断方式：判断文件名末尾是否是'.json'
                        //判断：true
                        $json_files[$L] = $models_folder_files[$f][$j];
                        $L++;
                    };
                };
                $json_files_after_combined_with_folders[$f] = $json_files;
                if ($json_files_after_combined_with_folders[$f] == array()) {
                    $json_files_after_combined_with_folders[$f] = null;
                };
            };

            if ($get_josn_files_type == 0) {
                //model目录下 文件/文件夹的名单 与 模组文件夹下的json文件名单重叠；
                return array_combine($models_names, $json_files_after_combined_with_folders);
                //目录文件可能很多，删除产地的所有变量减少服务器占用内存
                unset($models_names, $models_for_get_json_files, $get_josn_files_type, $json_files_after_combined_with_folders, $json_files, $models_folders, $models_folder_files, $models_folder, $i, $L, $f, $j);
            } else if ($get_josn_files_type == 1) {
                //model目录文件夹名称名单 + 模型文件夹目录文件名称名单
                $get_josn_files_1 = array();
                $get_josn_files_1['name'] = $models_names;
                $get_josn_files_1['files'] = $json_files_after_combined_with_folders;
                return $get_josn_files_1;
                //目录文件可能很多，删除所有产生的变量减少服务器占用内存
                unset($get_josn_files_1);
                unset($models_names, $models_for_get_json_files, $get_josn_files_type, $json_files_after_combined_with_folders, $json_files, $models_folders, $models_folder_files, $models_folder, $i, $L, $f, $j);
            } else if ($get_josn_files_type == 2) {
                $get_josn_files_2_i = 0;
                $get_josn_files_2 = array();
                for ($get_josn_files_2_a = 0; $get_josn_files_2_a < count($models_names); $get_josn_files_2_a++) {
                    if (!$json_files_after_combined_with_folders[$get_josn_files_2_a] == null) {
                        for ($get_josn_files_2_c = 0; $get_josn_files_2_c < count($json_files_after_combined_with_folders[$get_josn_files_2_a]); $get_josn_files_2_c++) {
                            $get_josn_files_2[$get_josn_files_2_i] = $models_names[$get_josn_files_2_a] . '/' . $json_files_after_combined_with_folders[$get_josn_files_2_a][$get_josn_files_2_c];
                            $get_josn_files_2_i++;
                        }
                    }
                }
                return $get_josn_files_2;
                //目录文件可能很多，删除所有产生的变量减少服务器占用内存
                unset($get_josn_files_2_i, $get_josn_files_2, $get_josn_files_2_a, $get_josn_files_2_c);
                unset($models_names, $models_for_get_json_files, $get_josn_files_type, $json_files_after_combined_with_folders, $json_files, $models_folders, $models_folder_files, $models_folder, $i, $L, $f, $j);
            } else if ($get_josn_files_type == 3) {
                $get_josn_files_3 = array();
                for ($get_josn_files_3_i = 0; $get_josn_files_3_i < count($models_names); $get_josn_files_3_i++) {
                    if (!$json_files_after_combined_with_folders[$get_josn_files_3_i] == null) {
                        for ($get_josn_files_3_a = 0; $get_josn_files_3_a < count($json_files_after_combined_with_folders[$get_josn_files_3_i]); $get_josn_files_3_a++) {
                            $get_josn_files_3[$models_names[$get_josn_files_3_i] . '/' . $json_files_after_combined_with_folders[$get_josn_files_3_i][$get_josn_files_3_a]] = $models_names[$get_josn_files_3_i] . '/' . $json_files_after_combined_with_folders[$get_josn_files_3_i][$get_josn_files_3_a];
                        }
                    }
                }
                return $get_josn_files_3;
                //目录文件可能很多，删除所有产生的变量减少服务器占用内存
                unset($get_josn_files_3, $get_josn_files_3_i, $get_josn_files_3_a);
                unset($models_names, $models_for_get_json_files, $get_josn_files_type, $json_files_after_combined_with_folders, $json_files, $models_folders, $models_folder_files, $models_folder, $i, $L, $f, $j);
            }
        };

        // print_r(get_json_files(1));
        // print_r(get_json_files(type));

        //------------------------------------type = 0 输出：花名册--------------

        // Array(
        //     [HK_3401] => Array(
        //         [0] => Normal.model3.json
        //         [1] => Normal.physics3.json
        //     )

        //     [HK416_805] => Array ( 
        //         [0] => Normal.model3.json
        //         [1] => Normal.physics3.json
        //     )

        //     [Kalina] => Array (
        //         [0] => Normal.model3.json
        //         [1] => Normal.physics3.json
        //     )

        //     [M4A1_4505] => Array (
        //         [0] => Normal.model3.json
        //     ) 

        //     [Pio] => Array ( 
        //         [0] => Model.json
        //     )

        //     [新建文件夹] = null

        //     [PL2DForTC.md] = null

        //     [和泉纱雾] => Array (
        //         [0] => Normal.model3.json
        //         [1] => Normal.physics3.json
        //     )
        // )

        //--------------------------------typr = 1 输出：文件夹名单和文件夹内json文件名单--------------

        // Array
        // (
        //     [name] => Array
        //         (
        //             [0] => HK416_3401
        //             [1] => HK416_805
        //             [2] => Kalina
        //             [3] => M4A1_4505
        //             [4] => Pio
        //             [5] => 新建文件夹
        //             [6] => PL2DForTC.md
        //             [7] => 和泉纱雾
        //         )

        //     [files] => Array
        //         (
        //             [0] => Array
        //                 (
        //                     [0] => Normal.model3.json
        //                     [1] => Normal.physics3.json
        //                 )

        //             [1] => Array
        //                 (
        //                     [0] => Normal.model3.json
        //                     [1] => Normal.physics3.json
        //                 )

        //             [2] => Array
        //                 (
        //                     [0] => Normal.model3.json
        //                     [1] => Normal.physics3.json
        //                 )

        //             [3] => Array
        //                 (
        //                     [0] => Normal.model3.json
        //                 )

        //             [4] => Array
        //                 (
        //                     [0] => Model.json
        //                 )
        //             [5] => null
        //             [6] => null
        //             [7] => Array
        //                 (
        //                     [0] => Normal.model3.json
        //                     [1] => Normal.physics3.json
        //                 )
        //         )

        // )


        //-------------------------------type = 2 输出：筛选出json文件(URL)--------------------------------

        // Array
        //   (
        //      [0] => HK416_3401/Normal.model3.json
        //      [1] => HK416_3401/Normal.physics3.json
        //      [2] => HK416_805/Normal.model3.json
        //      [3] => HK416_805/Normal.physics3.json
        //      [4] => Kalina/Normal.model3.json
        //      [5] => Kalina/Normal.physics3.json
        //      [6] => M4A1_4505/Normal.model3.json
        //      [7] => Pio/Model.json
        //      [8] => 和泉纱雾/Normal.model3.json
        //      [9] => 和泉纱雾/Normal.physics3.json
        //   )


        //-------------------------------type = 3 输出：筛选出json文件(URL+花名册)--------------------------------

        // Array
        //   (
        //      [HK416_3401/Normal.model3.json] => HK416_3401/Normal.model3.json
        //      [HK416_3401/Normal.physics3.json] => HK416_3401/Normal.physics3.json
        //      [HK416_805/Normal.model3.json] => HK416_805/Normal.model3.json
        //      [HK416_805/Normal.physics3.json] => HK416_805/Normal.physics3.json
        //      [Kalina/Normal.model3.json] => Kalina/Normal.model3.json
        //      [Kalina/Normal.physics3.json] => Kalina/Normal.physics3.json
        //      [M4A1_4505/Normal.model3.json] => M4A1_4505/Normal.model3.json
        //      [Pio/Model.json] => Pio/Model.json
        //      [和泉纱雾/Normal.model3.json] => 和泉纱雾/Normal.model3.json
        //      [和泉纱雾/Normal.physics3.json] => 和泉纱雾/Normal.physics3.json
        //   )


        //------------------------------------------------------end---原创不易-------------------------------------




        echo '<div class="P-main-content">
        <div class="P-navigation-bar">
        <div style="text-align: center;"><h3>PL2DForTC 0.5</h3></div>
        <ul class="P-tab">
        <li class="item0" tabindex="1" onclick="nav_bar(0)">模型目录</li>
        <li class="item1" tabindex="1" onclick="nav_bar(1)">模型选择</li>
        <li class="item2" tabindex="1" onclick="nav_bar(2)">页面设置</li>
        <li class="item3" tabindex="1" onclick="nav_bar(3)">画布设置</li>
        <li class="item4" tabindex="1" onclick="nav_bar(4)">模型设置</li>
        <li class="item5" tabindex="1" onclick="nav_bar(5)">其他设置</li>
        </ul>
        <button class="btn" type="button" onclick="detection_files(1)">检查并解锁保存按钮</button>
        </div>
        </div>';



        //目录树渲染
        $models_tree = get_json_files(1);
        $onclick_url = Helper::options()->pluginUrl . '/PL2DForTC/models/';

        echo '<div class="PL-main-content-tree P-main-content nav-bar-0"><div class="tree-box">
        <h2 style="text-align: center;">models目录(已筛选出josn文件)</h2>
        <ol class="tree">
            <input class="tree-input" type="checkbox" id="Wenjian0" checked="checked">
            <label class="tree-label" for="Wenjian1">PL2DForTC / models </label>
            <ol>';
        for ($tree_model_name = 0; $tree_model_name < count($models_tree['name']); $tree_model_name++) { //6个文件夹---*6
            echo '<li calss="tree-li">
            <input class="tree-input" type="checkbox" id="Wenjianjia' . $tree_model_name . '" checked="checked">
            <label class="tree-label" for="Wenjianjia' . $tree_model_name . '">' . $models_tree['name'][$tree_model_name] . '</label>
            <ol>';

            if ($models_tree['files'][$tree_model_name] == null) {
                echo '<li class="file tree-li">没有json文件</li>';
            } else {
                for ($tree_model_files_names = 0; $tree_model_files_names < count($models_tree['files'][$tree_model_name]); $tree_model_files_names++) {
                    echo '<li class="file tree-li"><a onclick="detection_files(2,\'' . $onclick_url . $models_tree['name'][$tree_model_name] . '/' . $models_tree['files'][$tree_model_name][$tree_model_files_names] . '\')">';
                    echo $models_tree['files'][$tree_model_name][$tree_model_files_names];
                    echo '</a></li>';
                };
            };


            echo '</ol></li>';
        };
        echo '</ol>
        </ol>
        </div>
        </div>';


        // 选择模型
        $choose_models_array = get_json_files(3);
        $choose_models = new Typecho_Widget_Helper_Form_Element_Radio(
            'choose_models',
            $choose_models_array,
            'HK416_805/normal.model3.json',
            _t('选择模型'),
            _t('选择插件 Models 目录下的模型，每个模型为一个文件夹，并确定配置文件是 <a>json</a>文件')
        );
        $choose_models->setAttribute('id', 'choose_models');
        $choose_models->setAttribute('class', 'P-ul P-ul-radio nav-bar-1');
        $modelurl = 1;
        $form->addInput($choose_models);

        // 选择外链模型
        $custom_model = new Typecho_Widget_Helper_Form_Element_Text('custom_model', NULL, NULL, _t('选择外链模型'), _t('在这里填入一个模型配置文件 <a>json</a> 文件的地址，可供使用外链模型，不填则使用插件目录下的模型'));
        $custom_model->setAttribute('class', 'P-ul P-ul-text nav-bar-1');
        $form->addInput($custom_model);

        $transparent = new Typecho_Widget_Helper_Form_Element_Radio('transparent', array(
            false => '开启调试(背景显示)',
            true => '关闭调试(背景隐藏)'
        ), true, '开启调试模式', '是否开启调试模式？开启后 背景/画布 将显示便于模型调试');
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
            _t('自定义看板娘所在的位置(还没写)')
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
            '说明：非默认模型请更换,<br> 默认: <br><div style="color:#fff;background:#383d45;padding: 5px;">model_PIXI.on("hit", (hitAreas) => {if (!hitAreas.includes("")) {model_PIXI.motion("Tap");}});<br></div><a href="https://github.com/guansss/pixi-live2d-display">文案查看githb</a> | <a href="https://www.bilibili.com/video/BV1Jp4y1W7s3">B站UP主说明视频</a>'
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

        $ppd = Helper::options()->pluginUrl;

        echo '<div id="PL2DForTC-div" class="PL2DForTC-div ' . Typecho_Widget::widget('Widget_Options')->Plugin('PL2DForTC')->position . '"><canvas id="PL2DForTC"></canvas></div>';
        echo "<script src='" . $ppd . "/PL2DForTC/js/live2dcubismcore.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/live2d.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/pixi.min.js'></script>" . "\n";
        echo "<script src='" . $ppd . "/PL2DForTC/js/guansss.min.js'></script>" . "\n";

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
