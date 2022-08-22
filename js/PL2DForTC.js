
window.onload = function () {
    $(".typecho-option").addClass("from-sub");
    $(".primary:button:submit").attr('disabled', true);
    $(".primary:button:submit").text('需检测来解锁');

    $('input').click(function () {
        $(".primary:button:submit").attr('disabled', true);
        $(".primary:button:submit").text('需检测来解锁');
    });
    $('label').click(function () {
        $(".primary:button:submit").attr('disabled', true);
        $(".primary:button:submit").text('需检测来解锁');
    });
    $(document).ready(function () {
        $(".P-main-content").wrapAll('<div class="PL_config"></div>')
    })

    //初始化

    $(".item0").addClass("active");
    $(".nav-bar-0").css("display", "block");
    $(".nav-bar-1").css("display", "none");
    $(".nav-bar-2").css("display", "none");
    $(".nav-bar-3").css("display", "none");
    $(".nav-bar-4").css("display", "none");
    $(".nav-bar-5").css("display", "none");
};

function detection_files(type, url) {
    if (type == 1) {
        undefined
        var radio_value = $('input[name="choose_models"]:checked').val();
        console.log(radio_value);
        var model_folder = '';
        if (!$('#custom_model-0-2').val()) {
            undefined
            model_folder = plugins_url + '/PL2DForTC/models/' + radio_value;
        } else {
            undefined
            model_folder = $('#custom_model-0-2').val();
        }
        console.log(model_folder);
    } else if (type == 2) {
        undefined
        model_folder = url;
    }

    $.ajax({

        url: model_folder,
        type: 'GET',
        complete: function (response) {
            if (response.status == 200) {

                //有效

                // //检查文件是否是json(url防漏)

                /** 判断字符串是否是json结构 */
                function isJSON(obj) {
                    var isjson = typeof (obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
                    return isjson;
                }


                //读取
                var json_file = $.parseJSON($.ajax({
                    url: model_folder,//json文件位置，文件名
                    dataType: "json", //返回数据格式为json
                    async: false
                }).responseText);


                //检查

                console.log(json_file);
                console.log('json文件: ' + isJSON(json_file));

                if (isJSON(json_file)) {

                    var json_data = [];
                    if ('version' in json_file || 'Version' in json_file || 'FileReferences' in json_file || 'fileReferences' in json_file || 'HitAreas' in json_file || 'hitAreas' in json_file ||
                        'textures' in json_file || 'Textures' in json_file || 'moc' in json_file || 'Moc' in json_file || 'model' in json_file || 'Model' in json_file || 'PhysicsSettings' in json_file || 'physicsSettings' in json_file || 'Meta' in json_file || 'meta' in json_file ||
                        'Curves' in json_file || 'curves' in json_file || 'UserData' in json_file || 'userData' in json_file) {//判断你是否是模型相关的文件
                        json_data['model_related'] = true;

                        if ('version' in json_file) {//提取版本号
                            json_data['version'] = json_file['version'];
                        } else if ('Version' in json_file) {
                            json_data['version'] = json_file['Version'];
                        } else {
                            json_data['version'] = '未知';
                        };

                        //确定文件类型
                        if ('FileReferences' in json_file || 'fileReferences' in json_file || 'textures' in json_file || 'Textures' in json_file) {//配置文件
                            json_data['file_type'] = 'configuration';
                            //提取模型文件名称
                            if ('FileReferences' in json_file) {//v3 up
                                if ('Moc' in json_file['FileReferences']) {
                                    json_data['model'] = json_file['FileReferences']['Moc'];
                                } else if ('moc' in json_file['FileReferences']) {
                                    json_data['model'] = json_file['FileReferences']['moc'];
                                }
                            } else if ('fileReferences' in json_file) {//v3 low
                                if ('Moc' in json_file['fileReferences']) {
                                    json_data['model'] = json_file['fileReferences']['Moc'];
                                } else if ('model' in json_file['fileReferences']) {
                                    json_data['model'] = json_file['fileReferences']['moc'];
                                }
                            } else if ('Model' in json_file) {//v1 up
                                json_data['model'] = json_file['Model'];
                            } else if ('model' in json_file) {//v1 low
                                json_data['model'] = json_file['model'];
                            } else {
                                json_data['model'] = '未知';
                            }
                        } else if ('PhysicsSettings' in json_file || 'physicsSettings' in json_file) {//物理控制器
                            json_data['file_type'] = 'physics';
                        } else if ('Curves' in json_file || 'curves' in json_file) {//预设动作
                            json_data['file_type'] = 'motion';
                        }
                    } else {
                        json_data['model_related'] = false;
                    }
                    console.log(json_data);

                    if (type == 1) {

                        if (!$('#custom_model-0-2').val() && json_data['model_related'] == true) {
                            if (json_data['file_type'] == 'configuration') {
                                swal({
                                    title: "验证通过",
                                    text: "经过检测,你选择的文件与模型有关" + "\n文件类型: 配置文件" + "\n模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n判断是否可用: 可用",
                                    icon: "success",
                                    button: "确定",
                                });
                            } else if (json_data['file_type'] == 'physics') {
                                swal({
                                    title: "警告",
                                    text: "经过检测,你选择的文件与模型有关" + "\n文件类型: 物理控制器文件" + "\n模型版本: " + json_data['version'] + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            } else if (json_data['file_type'] == 'motion') {
                                swal({
                                    title: "警告",
                                    text: "经过检测,你选择的文件与模型有关" + "\n文件类型: 预设动作文件" + "\n模型版本: " + json_data['version'] + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            } else {
                                swal({
                                    title: "警告",
                                    text: "经过检测,无法确定选择的文件类型" + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            }
                            $(".primary:button:submit").attr('disabled', false);
                            $(".primary:button:submit").text('保存设置');

                        } else if (json_data['model_related'] == true) {
                            if (json_data['file_type'] == 'configuration') {
                                swal({
                                    title: "验证通过",
                                    text: "经过检测,你输入的URL与模型有关" + "\n文件类型: 配置文件" + "\n模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n判断是否可用: 可用",
                                    icon: "success",
                                    button: "确定",
                                });
                            } else if (json_data['file_type'] == 'physics') {
                                swal({
                                    title: "警告",
                                    text: "经过检测,你输入的URL与模型有关" + "\n文件类型: 物理控制器文件" + "\n模型版本: " + json_data['version'] + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            } else if (json_data['file_type'] == 'motion') {
                                swal({
                                    title: "警告",
                                    text: "经过检测,你输入的URL与模型有关" + "\n文件类型: 预设动作文件" + "\n模型版本: " + json_data['version'] + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            } else {
                                swal({
                                    title: "警告",
                                    text: "经过检测,无法确定URL文件类型" + "\n判断是否可用: 不确定\n文件得是模型配置文件",
                                    icon: "warning",
                                    button: "确定",
                                });
                            }
                            $(".primary:button:submit").attr('disabled', false);
                            $(".primary:button:submit").text('保存设置');
                        } else {
                            swal({
                                title: "警告",
                                text: "经过检测,文件与模型无关" + "\n判断是否可用: 不可用\n文件得是模型配置文件",
                                icon: "warning",
                                button: "确定",
                            });
                        }

                    } else if (type == 2) {

                        if (json_data['file_type'] == 'configuration') {
                            swal({
                                title: "该文件为: 配置文件",
                                text: "模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n判断是否可用: 可用",
                                icon: "success",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'physics') {
                            swal({
                                title: "该文件为: 物理控制器文件",
                                text: "模型版本: " + json_data['version'] + "\n判断是否可用: 不确定",
                                icon: "success",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'motion') {
                            swal({
                                title: "该文件为: 预设动作文件",
                                text: "模型版本: " + json_data['version'] + "\n判断是否可用: 不确定",
                                icon: "success",
                                button: "确定",
                            });
                        } else {
                            swal({
                                title: "无法判断文件类型",
                                text: "判断是否可用: 不确定",
                                icon: "warning",
                                button: "确定",
                            });
                        }

                    }
                } else {
                    swal({
                        title: "错误",
                        text: "经过检测,选择/URl不是josn文件",
                        icon: "error",
                        button: "确定",
                    });
                }

            } else {
                swal({
                    title: "错误",
                    text: "经过检测,选择/URl不是有效地址",
                    icon: "error",
                    button: "确定",
                });
            }
        }
    });
};


function nav_bar(type) {
    if (type == 0) {
        $(".item0").addClass("active");
        $(".item1").removeClass("active");
        $(".item2").removeClass("active");
        $(".item3").removeClass("active");
        $(".item4").removeClass("active");
        $(".item5").removeClass("active");

        $(".nav-bar-0").css("display", "block");
        $(".nav-bar-1").css("display", "none");
        $(".nav-bar-2").css("display", "none");
        $(".nav-bar-3").css("display", "none");
        $(".nav-bar-4").css("display", "none");
        $(".nav-bar-5").css("display", "none");
    } else if (type == 1) {
        $(".item0").removeClass("active");
        $(".item1").addClass("active");
        $(".item2").removeClass("active");
        $(".item3").removeClass("active");
        $(".item4").removeClass("active");
        $(".item5").removeClass("active");

        $(".nav-bar-0").css("display", "none");
        $(".nav-bar-1").css("display", "block");
        $(".nav-bar-2").css("display", "none");
        $(".nav-bar-3").css("display", "none");
        $(".nav-bar-4").css("display", "none");
        $(".nav-bar-5").css("display", "none");
    } else if (type == 2) {
        $(".item0").removeClass("active");
        $(".item1").removeClass("active");
        $(".item2").addClass("active");
        $(".item3").removeClass("active");
        $(".item4").removeClass("active");
        $(".item5").removeClass("active");

        $(".nav-bar-0").css("display", "none");
        $(".nav-bar-1").css("display", "none");
        $(".nav-bar-2").css("display", "block");
        $(".nav-bar-3").css("display", "none");
        $(".nav-bar-4").css("display", "none");
        $(".nav-bar-5").css("display", "none");
    } else if (type == 3) {
        $(".item0").removeClass("active");
        $(".item1").removeClass("active");
        $(".item2").removeClass("active");
        $(".item3").addClass("active");
        $(".item4").removeClass("active");
        $(".item5").removeClass("active");

        $(".nav-bar-0").css("display", "none");
        $(".nav-bar-1").css("display", "none");
        $(".nav-bar-2").css("display", "none");
        $(".nav-bar-3").css("display", "block");
        $(".nav-bar-4").css("display", "none");
        $(".nav-bar-5").css("display", "none");
    } else if (type == 4) {
        $(".item0").removeClass("active");
        $(".item1").removeClass("active");
        $(".item2").removeClass("active");
        $(".item3").removeClass("active");
        $(".item4").addClass("active");
        $(".item5").removeClass("active");

        $(".nav-bar-0").css("display", "none");
        $(".nav-bar-1").css("display", "none");
        $(".nav-bar-2").css("display", "none");
        $(".nav-bar-3").css("display", "none");
        $(".nav-bar-4").css("display", "block");
        $(".nav-bar-5").css("display", "none");
    } else if (type == 5) {
        $(".item0").removeClass("active");
        $(".item1").removeClass("active");
        $(".item2").removeClass("active");
        $(".item3").removeClass("active");
        $(".item4").removeClass("active");
        $(".item5").addClass("active");

        $(".nav-bar-0").css("display", "none");
        $(".nav-bar-1").css("display", "none");
        $(".nav-bar-2").css("display", "none");
        $(".nav-bar-3").css("display", "none");
        $(".nav-bar-4").css("display", "none");
        $(".nav-bar-5").css("display", "block");
    }
}


/*-----------------------------------------------艺术字：真不错！------------------------------------------------*/

console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
Function.prototype.makeMulti = function () {
    let l = new String(this)
    l = l.substring(l.indexOf("/*") + 3, l.lastIndexOf("*/"))
    return l
}
let stringArt = function () {/*
             _                  _                           __     __     ___        _                   
            (_)                | |                         /_ |   / /    / _ \      | |                  
             _    __ _    ___  | | __  ___    ___   _ __    | |  / /_   | (_) |     | |_    ___    _ __  
            | |  / _` |  / __| | |/ / / __|  / _ \ |  _ \   | | |  _ \   > _ <      | __|  / _ \  |  _ \ 
            | | | (_| | | (__  |   <  \__ \ |  __/ | | | |  | | | (_) | | (_) |  _  | |_  | (_) | | |_) |
            | |  \__,_|  \___| |_|\_\ |___/  \___| |_| |_|  |_|  \___/   \___/  (_)  \__|  \___/  | .__/ 
           _/ |                                                                                   | |    
          |__/                                                                                    |_|    
        */}
console.log(stringArt.makeMulti());
console.log("\n %c jacksen168.top %c https://www.jacksen168.top/index.php/archives/PL2DForTC.html \n", "color: #fadfa3; background: #030307; padding:5px 0;", "background: #000000; padding:5px 0;");

Function.prototype.makeMulti = function () {
    let l = new String(this)
    l = l.substring(l.indexOf("/*") + 3, l.lastIndexOf("*/"))
    return l
}
let stringArt1 = function () {/*
             ____    _       ____    ____    _____                  _____    ____ 
            |  _ \  | |     |___ \  |  _ \  |  ___|   ___    _ __  |_   _|  / ___|
            | |_) | | |       __) | | | | | | |_     / _ \  |  __|   | |   | |    
            |  __/  | |___   / __/  | |_| | |  _|   | (_) | | |      | |   | |___ 
            |_|     |_____| |_____| |____/  |_|      \___/  |_|      |_|    \____|   0.5
                                                                                  
        */}
console.log(stringArt1.makeMulti());

console.log("\n %c PL2DForTC v0.5 %c https://github.com/jacksen168/PL2DForTC \n", "color: #fadfa3; background: #030307; padding:5px 0;", "background: #000000; padding:5px 0;");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");
console.log("Live 2D 看板娘效果使用PL2DForTC实现");
console.log("-----------------------------------------------------------------------------------------");

