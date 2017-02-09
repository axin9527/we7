<?php
/**
 * 投票圆梦计划模块处理程序
 *
 * @author alan51
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Alan_ticketModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
	}
}