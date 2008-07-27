<?php
class subsystem_syslog
{
	private $log;

	public function error($subsystem,$function,$message = '',$hide = false)
	{
		global $cfg;

		$this->log[] = array(0,$subsystem,$function,$message);
		if(isset($cfg['etc']['show_errors']) && $cfg['etc']['show_errors'] == 1 && !$hide)
		{
			echo('<br /><div style="background:#222;color:#555;font-size:10px;font-family:\'Courier New\',monospace;text-align:left;">');
			echo('<div style="background:#111;border-left: 5px solid #F00;color:#EEE;padding-left:10px;font-size:12px;">Subsystem: <b>'.$subsystem.'</b><br />Function: <b>'.$function.'</b><br />Message: <b>'.$message.'</b></div>');
			echo('</div>');
		}
	}
	public function success($subsystem,$function,$message = '',$hide = true)
	{
		global $cfg;

		$this->log[] = array(1,$subsystem,$function,$message);
		if(isset($cfg['etc']['show_errors']) && $cfg['etc']['show_errors'] == 1 && !$hide)
		{
			echo('<br /><div style="background:#222;color:#555;font-size:10px;font-family:Tahoma;text-align:left;">');
			echo('<div style="background:#111;border-left: 5px solid #0F0;color:#EEE;padding-left:10px;font-size:11px;">Subsystem: <b>'.$subsystem.'</b><br />Function: <b>'.$function.'</b><br />Message: <b>'.$message.'</b></div>');
			echo('</div>');
		}
	}
	public function log()
	{
		if(count($this->log) > 0)
		{
			echo('<table border="1" width="800" cellspacing="0" style="margin:0 auto;border: 1px solid #222;border-collapse:collapse;font-family:monospace;font-size:12px;color:#000;background:#FFF;text-align:left;"><thead><tr style="background:#FF9;"><th>Status</th><th>Subsystem</th><th>Function</th><th>Message</th></tr></thead><tbody>');
			foreach($this->log as $item)
			{
				echo((($item[0] == 0)?'<tr style="color:#F00;">':'<tr>').'<td>'.(($item[0] == 0)?'<strong>Error</strong>':'Success').'</td><td>'.$item[1].'</td><td>'.$item[2].'</td><td>'.$item[3].'</td></tr>');
			}
			echo('</tbody></table>');
		}
	}

	public function permissions_error($msg)
	{
		global $tpl;
		$tpl->load('permissions_error');
		$tpl->inc('permissions_error');
		$tpl->assign('PERMISSIONS_ERROR_MESSAGE',$msg);
		$tpl->assign('SITE_TITLE','{L_ERROR} &ndash; {SITE_HEADER}');
		$tpl->display();
	}
	public function alert_error($msg)
	{
		global $tpl;
		$tpl->load('alert_error');
		$tpl->inc('alert_error');
		$tpl->assign('ALERT_ERROR_MESSAGE',$msg);
		$tpl->assign('SITE_TITLE','{L_ERROR} &ndash; {SITE_HEADER}');
		$tpl->display();
	}
	public function alert_success($msg)
	{
		global $tpl;
		$tpl->load('alert_success');
		$tpl->inc('alert_success');
		$tpl->assign('ALERT_SUCCESS_MESSAGE',$msg);
		$tpl->assign('SITE_TITLE','{L_SUCCESS} &ndash; {SITE_HEADER}');
		$tpl->display();
	}
}

$syslog = new subsystem_syslog();
?>
