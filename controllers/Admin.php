<?php

namespace Rhymix\Modules\Dice\Controllers;

use Rhymix\Modules\Dice\Models\Config;
use Context;
use ModuleModel;

/**
 * 라이믹스 주사위 모듈
 * 
 * Copyright (c) 2026 Lastorder-DC
 * Licensed under GPLv2
 */
class Admin extends Base
{
	/**
	 * 관리자 설정 화면
	 * 
	 * @return void
	 */
	public function dispDiceAdminConfig()
	{
		// 현재 설정 가져오기
		$config = Config::getConfig();
		Context::set('config', $config);
		
		// 모든 모듈 목록 가져오기
		$oModuleModel = ModuleModel::getInstance();
		$module_list = $oModuleModel->getMidList();
		Context::set('module_list', $module_list);
		
		// 템플릿 설정
		$this->setTemplatePath($this->module_path . 'views/admin/');
		$this->setTemplateFile('config');
	}
	
	/**
	 * 관리자 설정 저장
	 * 
	 * @return object
	 */
	public function procDiceAdminInsertConfig()
	{
		$config = new \stdClass();
		
		// 적용할 모듈 설정
		$enabled_modules = Context::get('enabled_modules');
		if ($enabled_modules && is_array($enabled_modules))
		{
			$config->enabled_modules = $enabled_modules;
		}
		else
		{
			$config->enabled_modules = [];
		}
		
		// 제외할 모듈 설정
		$excluded_modules = Context::get('excluded_modules');
		if ($excluded_modules && is_array($excluded_modules))
		{
			$config->excluded_modules = $excluded_modules;
		}
		else
		{
			$config->excluded_modules = [];
		}
		
		// 설정 저장
		$result = Config::setConfig($config);
		
		if (!$result->toBool())
		{
			return $result;
		}
		
		$this->setMessage('success_updated');
		
		$returnUrl = Context::get('success_return_url') ?: getNotEncodedUrl('', 'module', 'admin', 'act', 'dispDiceAdminConfig');
		$this->setRedirectUrl($returnUrl);
	}
}
