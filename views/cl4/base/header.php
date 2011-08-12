<header>
	<div class="page_top_wrapper">
		<div class="page_top">
			<div class="language_options"><?php if ( ! empty($language_options)) { ?><span class="language_label"><?php echo __('Language: '); ?></span><?php echo $language_options; ?><?php } ?></div>
			<div class="clear"></div>
			<div class="page_top_logo"><a href="<?php echo URL::base(); ?>"><?php echo HTML::chars(SHORT_NAME . ' v' . APP_VERSION); if (isset($pageTitle) && trim($pageTitle) != '') echo ' - ' . HTML::chars($pageTitle); ?></a></div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="nav_wrapper_repeat">
		<div class="nav_wrapper">
			<?php // displays on the right hand side, includes login, logout and profile link ?>
			<nav class="basic_nav main_nav logged_in_nav">
				<ul>
					<?php if ($logged_in) { ?>
					<li class="logout"><?php echo HTML::anchor(Route::get('login')->uri(array('action' => 'logout')), '<img src="/images/nav/logout.gif" width="12" height="12" alt="' . HTML::chars(__('Logout')) . '"> ' . HTML::chars(__('Logout'))); ?></li>
					<li class="nav_divider"></li>
					<li class="account"><?php echo HTML::anchor(Route::get('account')->uri(array('action' => 'profile')), '<img src="/images/nav/my_account.gif" width="10" height="13" alt="' . HTML::chars(__('My Account')) . '"> ' . HTML::chars(__('My Account'))); ?></li>
					<?php } else { ?>
					<li class="login"><?php echo HTML::anchor(Route::get('login')->uri(), '<img src="/images/nav/logout.gif" width="10" height="13" alt="' . HTML::chars(__('Login')) . '"> ' . HTML::chars(__('Login'))); ?></li>
					<?php } // if logged in ?>
					<li class="nav_divider"></li>
				</ul>
				<?php if ($logged_in) { ?>
				<div class="page_top_user_info"><span class="login_in_as">Logged in as</span> <?php echo HTML::chars($user->first_name . ' ' . $user->last_name); ?></div>
				<?php } // if logged in ?>
			</nav>
			<nav class="basic_nav main_nav">
				<ul>
					<li class="home"><?php echo HTML::anchor('', __('Home')); ?></li>
					<li class="nav_divider"></li>
					<li class="aboutus"><?php echo HTML::anchor(Route::get('pages')->uri(array('page' => 'aboutus')), __('About Us')); ?>
						<ul class="sub_nav">
							<li class="ourpeople"><?php echo HTML::anchor(Route::get('pages_section')->uri(array('section' => 'aboutus', 'page' => 'ourpeople')), __('Our People')); ?></li>
						</ul>
					</li>
					<?php if ($logged_in && Auth::instance()->allowed('cl4admin')) { ?>
					<li class="nav_divider"></li>
					<li class="dbadmin"><?php echo HTML::anchor(Route::get('cl4admin')->uri(), __('DB Admin')); ?>
						<ul class="sub_nav">
							<?php if (Auth::instance()->allowed('useradmin/index')) { ?>
							<li class="useradmin"><?php echo HTML::anchor(Route::get('useradmin')->uri(), __('User Admin')); ?></li>
							<?php } ?>
							<?php if (Auth::instance()->allowed('cl4admin/model_create')) { ?>
							<li class="modelcreate"><?php echo HTML::anchor(Route::get('cl4admin')->uri(array('model' => 'a', 'action' => 'model_create')), __('Model Create')); ?></li>
							<?php } ?>
							<?php if (Auth::instance()->allowed('dbchange/index')) { ?>
							<li class="dbchange"><?php echo HTML::anchor(Route::get('dbchange')->uri(), __('DB Change')); ?></li>
							<?php } ?>
						</ul>
					</li>
					<?php } ?>
				</ul>
			</nav>
		</div>
	</div>
</header>