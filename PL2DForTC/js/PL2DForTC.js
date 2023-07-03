// 禁用按钮
function disableClicking() {
    var submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = '需检测来解锁';
}

//启用按钮
function enableClicking() {
    var submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.textContent = '保存设置';
}

//导航栏
function nav_bar(event, tabClassName) {
    // 获取所有标签和内容元素
    var tabs = document.getElementsByClassName('P-tab-bar');
    var tabContents = document.getElementsByClassName('P-ul');

    // 移除所有标签和内容的 'active' 类/ul全部不显示
    document.getElementsByClassName('nav-bar-0')[0].style.display = "none";
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }

    // 向点击的标签和内容添加 'active' 类/显示
    event.target.classList.add('active');
    var elements = document.getElementsByClassName(tabClassName);

    // 遍历元素集合，将每个元素的 display 属性设置为 "block"
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.display = 'block';
    }

    //回到顶部
    window.scrollTo(0, 0);
}

//智能筛选/未经筛选tab切换
function openTab(event, tabId) {
    // 获取所有标签和内容元素
    var tabs = document.getElementsByClassName('tab');
    var tabContents = document.getElementsByClassName('tab-main-content');

    // 移除所有标签和内容的 'active' 类
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
        tabContents[i].classList.remove('active');
    }

    // 向点击的标签和内容添加 'active' 类
    event.target.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

//显示选择模型按钮
function show_choose_button(value1, value2) {

    // document.querySelector('input[name="choose_models"]').disabled = true;

    // 获取要插入按钮的父容器元素
    var tabContainer = document.getElementById('choose_models_tab1');
    var tabContainer2 = document.getElementById('choose_models_tab2');

    // 定义按钮文本的数组
    var buttonValues = JSON.parse(value1);
    var buttonValues2 = JSON.parse(value2);

    if (buttonValues == '') {
        var h3 = document.createElement('h3');
        h3.textContent = '未筛选出模型文件';
        h3.classList.add('choose-tab-button');
        tabContainer.appendChild(h3);
    }
    if (buttonValues2 == '') {
        var h3 = document.createElement('h3');
        h3.textContent = 'models目录下没有json文件';
        h3.classList.add('choose-tab-button');
        tabContainer2.appendChild(h3);
    }

    // 遍历按钮文本数组
    for (var i = 0; i < buttonValues.length; i++) {
        // 创建按钮元素
        var button = document.createElement('a');

        var text = buttonValues[i];
        // 设置按钮的文本内容
        button.textContent = text;
        //设置按钮值
        button.value = text;
        //设置按钮点击函数
        button.addEventListener('click', choose_button)
        //设置按钮class
        button.classList.add('choose-tab-button');
        //设置按钮默认被选择class
        var textvalue = document.querySelector('input[name="choose_models"]').value;
        if (textvalue == text) {
            button.classList.add('active');
        }
        // 将按钮添加到父容器中
        tabContainer.appendChild(button);
    }

    // 遍历按钮文本数组
    for (var i = 0; i < buttonValues2.length; i++) {
        // 创建按钮元素
        var button = document.createElement('a');

        var text = buttonValues2[i];
        // 设置按钮的文本内容
        button.textContent = text;
        //设置按钮值
        button.value = text;
        //设置按钮点击函数
        button.addEventListener('click', choose_button)
        //设置按钮class
        button.classList.add('choose-tab-button');
        //设置按钮默认被选择class
        var textvalue = document.querySelector('input[name="choose_models"]').value;
        if (textvalue == text) {
            button.classList.add('active');
        }
        // 将按钮添加到父容器中
        tabContainer2.appendChild(button);
    }


}

//选择模型按钮
function choose_button(event) {
    // 获取所有标签和内容元素
    var buttons = document.getElementsByClassName('choose-tab-button');

    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('active');
    }

    // 向点击的标签和内容添加 'active' 类
    event.target.classList.add('active');
    var value = event.target.value;
    console.log(value)
    document.querySelector('input[name="choose_models"]').value = value;
}

//打印log
function print_log(project) {
    /*-----------------------------------------------艺术字：真不错！------------------------------------------------*/

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
            |_|     |_____| |_____| |____/  |_|      \___/  |_|      |_|    \____|
                                                                                  
        */}
    console.log(stringArt1.makeMulti());

    console.log("\n %c " + project['name'] + " v" + project['version'] + " %c https://github.com/jacksen168/PL2DForTC \n", "color: #fadfa3; background: #030307; padding:5px 0;", "background: #000000; padding:5px 0;");
    console.log("-----------------------------------------------------------------------------------------");
    console.log("Live 2D 看板娘效果使用PL2DForTC实现");
    console.log("-----------------------------------------------------------------------------------------");
};

//ajax请求
function sendAjaxRequest(url, method, callback) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(null, xhr.responseText);
            } else {
                callback(new Error('请求失败：' + xhr.status));
            }
        }
    };

    xhr.open(method, url, true);
    xhr.send();
}

//检查文件
function detection_files(type, url) {
    //选择模型文件input值/外链模型文件input值
    var custom_model_value = document.querySelector('input[name="custom_model"]').value;
    var radio_value = document.querySelector('input[name="choose_models"]').value;
    //设置模型文件地址
    if (type == 1) {
        //解锁检查
        undefined
        var model_file_url = '';
        if (!custom_model_value) {
            //选择模型文件
            undefined
            model_file_url = plugins_url + '/PL2DForTC/models/' + radio_value;
        } else {
            //外链模型文件
            undefined
            model_file_url = custom_model_value;
        }
        console.log(model_file_url);
    } else if (type == 2) {
        //目录树检查
        undefined
        model_file_url = url;
    }

    sendAjaxRequest(model_file_url, 'GET', function (error, responseText) {
        if (!error) {
            //请求正常

            //试错
            try {
                //解析
                var json_file = JSON.parse(responseText);

                //解析成功后运行下面代码
                //判断json文件类型
                var json_data = [];
                if ('version' in json_file || 'Version' in json_file || 'FileReferences' in json_file || 'fileReferences' in json_file || 'HitAreas' in json_file || 'hitAreas' in json_file ||
                    'textures' in json_file || 'Textures' in json_file || 'moc' in json_file || 'Moc' in json_file || 'model' in json_file || 'Model' in json_file || 'PhysicsSettings' in json_file || 'physicsSettings' in json_file || 'Meta' in json_file || 'meta' in json_file ||
                    'Curves' in json_file || 'curves' in json_file || 'UserData' in json_file || 'userData' in json_file) {
                    //判断你是否是模型相关的文件
                    json_data['model_related'] = true;

                    //提取版本号
                    if ('version' in json_file) {
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
                    } else if ('PhysicsSettings' in json_file || 'physicsSettings' in json_file) {
                        //物理控制器
                        json_data['file_type'] = 'physics';
                    } else if ('Curves' in json_file || 'curves' in json_file) {
                        //预设动作
                        json_data['file_type'] = 'motion';
                    }
                } else {
                    //与模型文件无关
                    json_data['model_related'] = false;
                }

                console.log(json_data);

                //提示框类型
                if (type == 1) {
                    //解锁检查
                    if (!custom_model_value && json_data['model_related'] == true) {
                        //选择模型文件
                        if (json_data['file_type'] == 'configuration') {
                            //模型配置文件
                            swal({
                                title: "文件可用",
                                text: "文件类型: 配置文件" + "\n模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n文件: " + radio_value,
                                icon: "success",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'physics') {
                            //物理控制器文件
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 物理控制器文件" + "\n模型版本: " + json_data['version'] + "\n文件: " + radio_value,
                                icon: "warning",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'motion') {
                            //预设动作文件
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 预设动作文件" + "\n模型版本: " + json_data['version'] + "\n文件: " + radio_value,
                                icon: "warning",
                                button: "确定",
                            });
                        } else {
                            //未知
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 未知" + "\n文件: " + radio_value,
                                icon: "warning",
                                button: "确定",
                            });
                        }
                        enableClicking();//启用按钮

                    } else if (json_data['model_related'] == true) {
                        //外链
                        if (json_data['file_type'] == 'configuration') {
                            //模型配置文件
                            swal({
                                title: "文件可用",
                                text: "文件类型: 配置文件" + "\n模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n链接: " + custom_model_value,
                                icon: "success",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'physics') {
                            //物理控制器文件
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 物理控制器文件" + "\n模型版本: " + json_data['version'] + "\n链接: " + custom_model_value,
                                icon: "warning",
                                button: "确定",
                            });
                        } else if (json_data['file_type'] == 'motion') {
                            //预设动作文件
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 预设动作文件" + "\n模型版本: " + json_data['version'] + "\n链接: " + custom_model_value,
                                icon: "warning",
                                button: "确定",
                            });
                        } else {
                            //未知
                            swal({
                                title: "文件可用性未知",
                                text: "文件类型: 未知" + "\n链接: " + custom_model_value,
                                icon: "warning",
                                button: "确定",
                            });
                        }
                        enableClicking();//启用按钮
                    } else {
                        //无关文件
                        swal({
                            title: "文件不可用性",
                            text: "文件类型: 未知" + "\n经过检测,文件与模型无关",
                            icon: "warning",
                            button: "确定",
                        });
                    }

                } else if (type == 2) {

                    if (json_data['file_type'] == 'configuration') {
                        swal({
                            title: "模型配置文件",
                            text: "模型版本: " + json_data['version'] + "\n骨骼文件: " + json_data['model'] + "\n判断是否可用: 可用",
                            icon: "success",
                            button: "确定",
                        });
                    } else if (json_data['file_type'] == 'physics') {
                        swal({
                            title: "物理控制器文件",
                            text: "模型版本: " + json_data['version'] + "\n判断是否可用: 不确定",
                            icon: "success",
                            button: "确定",
                        });
                    } else if (json_data['file_type'] == 'motion') {
                        swal({
                            title: "预设动作文件",
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

            } catch (error) {
                // 这里是对解析失败后的错误处理
                console.error('JSON 解析错误:', error);
                swal({
                    title: "错误",
                    text: "JSON 解析错误,文件不是正确的json文件:\n" + model_file_url + '\n错误:\n' + error,
                    icon: "error",
                    button: "确定",
                });
            }

        } else {
            swal({
                title: "错误",
                text: "经过检测,选择/URl不是有效地址:\n" + model_file_url + '\n' + error,
                icon: "error",
                button: "确定",
            });
        }
    });

};


window.onload = function () {
    //页面加载完成后开始初始化

    // 保存按钮悬浮
    var options = document.querySelectorAll('.typecho-option');
    for (var i = 0; i < options.length; i++) {
        options[i].classList.add('from-sub');
    }

    //禁用按钮
    disableClicking();

    // 添加点击事件监听器   监听页面的修改来禁用按钮
    var inputs = document.querySelectorAll('input');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('click', disableClicking);
    }

    var labels = document.querySelectorAll('label');
    for (var j = 0; j < labels.length; j++) {
        labels[j].addEventListener('click', disableClicking);
    }

    var links = document.querySelectorAll('a');
    for (var k = 0; k < links.length; k++) {
        links[k].addEventListener('click', disableClicking);
    }

    //修改排版函数
    function wrapAll(elements, wrapper) {
        // 获取第一个被包裹元素的父级节点
        var parent = elements[0].parentNode;

        // 在第一个被包裹元素之前插入包裹容器
        parent.insertBefore(wrapper, elements[0]);

        // 将被包裹的元素移动到包裹容器中
        for (var i = 0; i < elements.length; i++) {
            wrapper.appendChild(elements[i]);
        }
    }

    //修改排版
    var mainContents = document.querySelectorAll('.P-main-content');
    var newWrapper = document.createElement('div');
    newWrapper.className = 'PL_config';

    wrapAll(mainContents, newWrapper);


    // .P-ul标签下的设置卡片全部不显示,注:目录树不在.P-ul下
    var tabContents = document.getElementsByClassName('P-ul');
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }
};