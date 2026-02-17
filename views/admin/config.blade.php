@load('config.scss')

{{-- 관리자 설정 화면 --}}

<div class="x_page-header">
	<h1>{{ $lang->cmd_dice }}</h1>
</div>

<ul class="x_nav x_nav-tabs">
	<li @class(['x_active' => $act === 'dispDiceAdminConfig'])>
		<a href="@url(['module' => 'admin', 'act' => 'dispDiceAdminConfig'])">{{ $lang->cmd_dice_general_config }}</a>
	</li>
</ul>

<form class="x_form-horizontal" action="./" method="post" id="dice_config">
	<input type="hidden" name="module" value="dice" />
	<input type="hidden" name="act" value="procDiceAdminInsertConfig" />
	<input type="hidden" name="success_return_url" value="{{ getRequestUriByServerEnviroment() }}" />
	<input type="hidden" name="xe_validator_id" value="modules/dice/views/admin/config/1" />

	@if (!empty($XE_VALIDATOR_MESSAGE) && $XE_VALIDATOR_ID == 'modules/dice/views/admin/config/1')
		<div class="message {{ $XE_VALIDATOR_MESSAGE_TYPE }}">
			<p>{{ $XE_VALIDATOR_MESSAGE }}</p>
		</div>
	@endif

	<section class="section">
		<div class="x_page-header">
			<h2>{{ $lang->cmd_dice_general_config }}</h2>
			<p>{{ $lang->about_dice }}</p>
			<p><small>{{ $lang->about_dice_format }}</small></p>
		</div>

		{{-- 적용할 모듈 선택 --}}
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_dice_enabled_modules }}</label>
			<div class="x_controls">
				<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
					<label class="x_block">
						<input type="checkbox" name="enabled_modules[]" value="all" 
							@checked(!isset($config->enabled_modules) || !is_array($config->enabled_modules) || count($config->enabled_modules) == 0) 
							onchange="if(this.checked) { Array.from(document.querySelectorAll('input[name=\'enabled_modules[]\']')).forEach(cb => { if(cb.value !== 'all') cb.checked = false; }); }" />
						{{ $lang->cmd_dice_module_all }}
					</label>
					@if(is_array($module_list))
						@foreach($module_list as $module_srl => $module_info)
							<label class="x_block">
								<input type="checkbox" name="enabled_modules[]" value="{{ $module_srl }}" 
									@checked(is_array($config->enabled_modules ?? null) && in_array($module_srl, $config->enabled_modules))
									onchange="if(this.checked) { document.querySelector('input[name=\'enabled_modules[]\'][value=all]').checked = false; }" />
								{{ $module_info->mid }} ({{ $module_info->browser_title }})
							</label>
						@endforeach
					@endif
				</div>
				<p class="x_help-block">{{ $lang->cmd_dice_enabled_modules_desc }}</p>
			</div>
		</div>

		{{-- 제외할 모듈 선택 --}}
		<div class="x_control-group">
			<label class="x_control-label">{{ $lang->cmd_dice_excluded_modules }}</label>
			<div class="x_controls">
				<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
					@if(is_array($module_list))
						@foreach($module_list as $module_srl => $module_info)
							<label class="x_block">
								<input type="checkbox" name="excluded_modules[]" value="{{ $module_srl }}" 
									@checked(is_array($config->excluded_modules ?? null) && in_array($module_srl, $config->excluded_modules)) />
								{{ $module_info->mid }} ({{ $module_info->browser_title }})
							</label>
						@endforeach
					@endif
				</div>
				<p class="x_help-block">{{ $lang->cmd_dice_excluded_modules_desc }}</p>
			</div>
		</div>
	</section>

	<div class="btnArea x_clearfix">
		<button type="submit" class="x_btn x_btn-primary x_pull-right">{{ $lang->cmd_registration }}</button>
	</div>

</form>
