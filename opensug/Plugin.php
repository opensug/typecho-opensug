<?php
/**
 * 为来访者提供搜索建议，使用户的搜索方便快捷。[<a target="_blank" href="https://support.qq.com/products/596940">反馈</a>]<br />支持<font color="#3369E8">百度</font>、<font color="#3369E8">谷歌</font>、<font color="#D50F25">雅虎</font>、Yandex、<font color="#009925">360好搜</font>、<font color="#F1941C">UC神马</font>、<font color="#3369E8">酷狗</font>、<font color="#D50F25">优酷</font>、<font color="#F1941C">淘宝</font>等结果源.
 * 
 * @package openSug
 * @author openSug.org
 * @version 1.0.0
 * @link https://www.opensug.eu.org
 */
defined( "__TYPECHO_ROOT_DIR__" ) || (
	header( "HTTP/1.1 404 Not Found"	) & 
	header( "Status: 404 Not Found" 	) & 
	exit
);

define("_Name_", basename(__DIR__));

class opensug_Plugin implements Typecho_Plugin_Interface{
	const _VERSION	= "1.0.0";	// 插件版本

    // 激活插件
	public static function activate(){
		Typecho_Plugin::factory("Widget_Archive")->footer = array(
			__CLASS__,
			"renderFooter"
		);
    }

    // 禁用插件
    public static function deactivate(){

	}

    // 插件调试
	public static function config(Typecho_Widget_Helper_Form $form){
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "ipt",			null, "s",							_t("[String]输入框ID"),				"绑定到输入框ID[Text]"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "XOffset",		null, "",							_t("[Nubmer]横向偏移:"),		"横向偏移量, 单位px, 正值向左偏移."));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "YOffset",		null, "",							_t("[Nubmer]纵向偏移:"),		"纵向偏移量, 单位px, 正值向上偏移."));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "bgcolor",		null, "#FFFFFF",					_t("[Color]背景颜色:"),		"提示框高亮选择的颜色"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "bgcolorHI",	null, "#F6F6F6",					_t("[Color]背景颜色[高亮]:"), "提示框高亮选择的颜色"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "borderColor",	null, "#E9E9E9",					_t("[Color]边框颜色:"),		"提示框的边框颜色"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "fontColor",	null, "#444444",					_t("[Color]字体颜色:"),		"字体颜色"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "fontColorHI",	null, "#000000",					_t("[Color]字体颜色[高亮]:"), "提示框高亮选择时字体颜色"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "fontFamily",	null, "",							_t("[String]字体系列:"),		"字体系列"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "fontSize",	null, "14px",						_t("[String]字体大小:"),		"字体大小,必须包含单位.例:1em、16px"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "padding",		null, "2px",						_t("[String]内填充:"),		""));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "radius",		null, "2px",						_t("[String]边框圆角:"),		""));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "shadow",		null, "0 16px 10px #00000080",		_t("[String]边框阴影:"), 		""));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Text( "width",		null, null,							_t("[Nubmer]宽度:"), 		"提示框宽度,留空继承父输入框宽度.单位px."));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Select( "sugSubmit", array(
			"true"		=> "选中提交",
			"false"		=> "手动提交"
		), true, _t("默认执行动作"), "选中提示框中词条时是否提交表单"));
		$form->addInput(new Typecho_Widget_Helper_Form_Element_Select( "source", array(
			""			=> "百度",
			"google"	=> "谷歌",
			"haoso"		=> "好搜",
			"kugou"		=> "酷狗",
			"yahoo"		=> "雅虎",
			"yandex"	=> "Yandex",
			"youku"		=> "优酷视频",
			"taobao"	=> "淘宝",
			"attayo"	=> "Attayo",
			"mgtv"		=> "芒果视频",
			"sm"		=> "神马搜索",
			"weibo"		=> "微博",
			"rambler"	=> "Rambler",
			"soft"		=> "软件",
			"naver"		=> "Naver",
			"car"		=> "新浪汽车",
			"car2"		=> "网易汽车",
			"qunar"		=> "去哪儿",
			"lagou"		=> "拉钩网"
	   	), null, _t("搜索引擎结果源:"), "可供选择谷歌、Bing、Yandex、360好搜、UC神马、酷狗、优酷、淘宝等结果源.[默认baidu]."));
    }
	
	// 个人用户的配置面板
	public static function personalConfig(Typecho_Widget_Helper_Form $form){

	}

	// 加载脚本库并且设置相关参数
	public static function renderFooter(){
		$db						= Typecho_Widget::widget('Widget_Options')->plugin(_Name_);
		$config					= array();
		$config["ipt"]			= $db->ipt;
		$config["bgcolor"]		= $db->bgcolor;
		$config["bgcolorHI"]	= $db->bgcolorHI;
		$config["borderColor"]	= $db->borderColor;
		$config["fontColor"]	= $db->fontColor;
		$config["fontColorHI"]	= $db->fontColorHI;
		$config["fontFamily"]	= $db->fontFamily;
		$config["fontSize"]		= $db->fontSize;
		$config["padding"]		= $db->padding;
		$config["radius"]		= $db->radius;
		$config["shadow"]		= $db->shadow;
		$config["source"]		= $db->source;
		$config["sugSubmit"]	= $db->sugSubmit;
		$config["width"]		= $db->width;
		$config["XOffset"]		= $db->XOffset;
		$config["YOffset"]		= $db->YOffset;

		if( ! isset( $config["ipt"] ) || strlen($config["ipt"]) == 0 ) return false;
		if( ! preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $config["bgcolor"] ) )			$config["bgcolor"]		= "";
		if( ! preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $config["bgcolorHI"] ) )			$config["bgcolorHI"]	= "";
		if( ! preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $config["borderColor"] ) )		$config["borderColor"]	= "";
		if( ! preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $config["fontColor"] ) )			$config["fontColor"]	= "";
		if( ! preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $config["fontColorHI"] ) )		$config["fontColorHI"]	= "";
		if( ! preg_match( "/^(?:-?\d+)?$/", $config["XOffset"] ) ) 									$config["XOffset"]		= "";
		if( ! preg_match( "/^(?:-?\d+)?$/", $config["YOffset"] ) ) 									$config["YOffset"]		= "";
		if( ! preg_match( "/^\d+$/", $config["fontColorHI"] ) )										$config["width"]		= "";
		if( ! preg_match( "/^(\d+(px|em))( (\d+(px|em))){0,3}$/", $config["padding"] ) )			$config["padding"]		= "";
		if( ! preg_match( "/^(\d+(px|em))( (\d+(px|em))){0,3}$/", $config["radius"] ) )				$config["radius"]		= "";
		if( ! preg_match( "/\b(\d+(?:px|em)(?:\s+\d+(?:px|em)){0,3})\b/", $config["fontSize"] ) )	$config["fontSize"]		= "14px";

		echo "\r\n<script type=\"text/javascript\" src=\"https://opensug.github.io/js/opensug.js\"></script>\r\n<script type=\"text/javascript\">\"use strict\";(function(){\r\n	var ipt = document[\"getElementById\"](\"{$config["ipt"]}\");\r\n	if( ipt != null && (\r\n		(ipt[\"getAttribute\"](\"type\") || \"\")[\"toLocaleLowerCase\"]() === \"search\" || \r\n		(ipt[\"getAttribute\"](\"type\") || \"\")[\"toLocaleLowerCase\"]() === \"text\") && \r\n	   	\"function\" === typeof( window[\"openSug\"] )\r\n	) window[\"openSug\"]( \"{$config["ipt"]}\", {\r\n		// 提示框的背景色。\r\n		bgcolor : \"{$config["bgcolor"]}\",\r\n\r\n		// 提示框的高亮背景色。\r\n		bgcolorHI : \"{$config["bgcolorHI"]}\",\r\n\r\n		// 提示框的边框颜色。\r\n		borderColor : \"{$config["borderColor"]}\",\r\n\r\n		// 提示框中文本的颜色。\r\n		fontColor : \"{$config["fontColor"]}\",\r\n\r\n		// 高亮显示提示框中的文本颜色。\r\n		fontColorHI : \"{$config["fontColorHI"]}\",\r\n\r\n		// 提示框中文本的字体。\r\n		fontFamily : \"{$config["fontFamily"]}\",\r\n\r\n		// 提示框中的文本字体大小。\r\n		fontSize : \"{$config["fontSize"]}\",\r\n\r\n		// 提示框的内边距。\r\n		padding : \"{$config["padding"]}\",\r\n\r\n		// 边界的圆角半径。\r\n		radius : \"{$config["radius"]}\",\r\n\r\n		// 边框的阴影效果。\r\n		shadow : \"{$config["shadow"]}\",\r\n\r\n		// 搜索提示框的数据源。\r\n		source : \"{$config["source"]}\",\r\n\r\n		// 选择提示时是否自动提交表单。\r\n		sugSubmit : {$config["sugSubmit"]},\r\n\r\n		// 提示框的宽度。\r\n		// 建议的空值。\r\n		width : \"{$config["width"]}\",\r\n\r\n		// 提示框相对于输入框的横向偏移。\r\n		// 负值向右移动。\r\n		XOffset : \"{$config["XOffset"]}\",\r\n\r\n		// 提示框相对于输入框的纵向偏移。\r\n		// 负值向下偏移。\r\n		YOffset : \"{$config["YOffset"]}\"\r\n\r\n	}, function(callback){\r\n		/*  ...  */\r\n	});		\r\n}(this));</script>\r\n";
	}
}