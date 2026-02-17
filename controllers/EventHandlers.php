<?php

namespace Rhymix\Modules\Dice\Controllers;

use Rhymix\Modules\Dice\Models\Config;

require_once __DIR__ . '/../libs/DiceCalc/Calc.php';
require_once __DIR__ . '/../libs/DiceCalc/CalcSet.php';
require_once __DIR__ . '/../libs/DiceCalc/CalcDice.php';
require_once __DIR__ . '/../libs/DiceCalc/CalcOperation.php';
require_once __DIR__ . '/../libs/DiceCalc/Random.php';

use DiceCalc\Calc;

/**
 * 라이믹스 주사위 모듈
 * 
 * Copyright (c) 2026 Lastorder-DC
 * Licensed under GPLv2
 */
class EventHandlers extends Base
{
	/**
	 * 트리거: 새 글 작성 전 실행
	 * 
	 * @param object $obj
	 */
	public function beforeInsertDocument($obj)
	{
		if (!$this->shouldProcess($obj->module_srl ?? null))
		{
			return;
		}
		
		$this->processDiceStrings($obj);
	}
	
	/**
	 * 트리거: 글 수정 전 실행
	 * 
	 * @param object $obj
	 */
	public function beforeUpdateDocument($obj)
	{
		if (!$this->shouldProcess($obj->module_srl ?? null))
		{
			return;
		}
		
		$this->processDiceStrings($obj);
	}
	
	/**
	 * 트리거: 새 댓글 작성 전 실행
	 * 
	 * @param object $obj
	 */
	public function beforeInsertComment($obj)
	{
		if (!$this->shouldProcess($obj->module_srl ?? null))
		{
			return;
		}
		
		$this->processDiceStrings($obj);
	}
	
	/**
	 * 트리거: 댓글 수정 전 실행
	 * 
	 * @param object $obj
	 */
	public function beforeUpdateComment($obj)
	{
		if (!$this->shouldProcess($obj->module_srl ?? null))
		{
			return;
		}
		
		$this->processDiceStrings($obj);
	}
	
	/**
	 * 설정에 따라 이 모듈에서 주사위 변환을 처리할지 결정
	 * 
	 * @param int $module_srl
	 * @return bool
	 */
	protected function shouldProcess($module_srl)
	{
		if (!$module_srl)
		{
			return false;
		}
		
		// module_srl을 mid로 변환
		$oModuleModel = \ModuleModel::getInstance();
		$mid = $oModuleModel->getMidByModuleSrl($module_srl);
		
		if (!$mid)
		{
			return false;
		}
		
		$config = Config::getConfig();
		
		// 제외할 모듈 체크 (설정은 mid 기준)
		if (isset($config->excluded_modules) && is_array($config->excluded_modules))
		{
			if (in_array($mid, $config->excluded_modules))
			{
				return false;
			}
		}
		
		// 적용할 모듈이 지정된 경우 (설정은 mid 기준)
		if (isset($config->enabled_modules) && is_array($config->enabled_modules) && count($config->enabled_modules) > 0)
		{
			return in_array($mid, $config->enabled_modules);
		}
		
		// 기본값: 모든 모듈에 적용
		return true;
	}
	
	/**
	 * 본문에서 {dicestring} 형태를 찾아서 변환
	 * 
	 * @param object $obj
	 */
	protected function processDiceStrings($obj)
	{
		// content 필드에서 주사위 문자열 처리
		if (isset($obj->content) && $obj->content)
		{
			$obj->content = $this->replaceDiceStrings($obj->content);
		}
	}
	
	/**
	 * 문자열에서 {expression} 패턴을 찾아 주사위/수식 결과로 변환
	 * 
	 * @param string $text
	 * @return string
	 */
	protected function replaceDiceStrings($text)
	{
		// {expression} 형태를 찾는 정규식
		// 예: {3d6}, {2d20+5}, {4d6h3}, {(4+4)*5}
		$pattern = '/\{([^}]+)\}/';
		
		$result = preg_replace_callback($pattern, function($matches) {
			$expression = trim($matches[1]);
			
			// 빈 문자열은 건너뛰기
			if ($expression === '')
			{
				return $matches[0];
			}
			
			try
			{
				// dicecalc로 변환 시도 (주사위 표현식뿐만 아니라 수식도 지원)
				$calc = new Calc($expression);
				$result = $calc();
				$infix = $calc->infix();
				
				// 결과 포맷: [원본표현식 → 상세결과 = 최종값]
				return '[' . $expression . ' → ' . $infix . ' = ' . $result . ']';
			}
			catch (\Exception $e)
			{
				// 에러가 발생하면 원본 반환
				return $matches[0];
			}
		}, $text);
		
		return $result;
	}
}
