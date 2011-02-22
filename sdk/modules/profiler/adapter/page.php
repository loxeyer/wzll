<?php

class Profiler_Adapter_Page extends Profiler_Adapter
{
	/**
	 * render profiler
	 */
	//{{{ show html
	public function render($profiler)
	{
?>
<style type="text/css">
.sdk table.profiler { width: 99%; margin: 0 auto 1em; border-collapse: collapse; }
.sdk table.profiler th,
.sdk table.profiler td { padding: 0.2em 0.4em; background: #fff; border: solid 1px #999; border-width: 1px 0; text-align: left; font-weight: normal; font-size: 1em; color: #111; text-align: right; }
.sdk table.profiler th.name { text-align: left; }
.sdk table.profiler tr.group th { font-size: 1.4em; background: #222; color: #eee; border-color: #222; }
.sdk table.profiler tr.group td { background: #222; color: #777; border-color: #222; }
.sdk table.profiler tr.group td.time { padding-bottom: 0; }
.sdk table.profiler tr.headers th { text-transform: lowercase; font-variant: small-caps; background: #ddd; color: #777; }
.sdk table.profiler tr.mark th.name { width: 40%; font-size: 1.2em; background: #fff; vertical-align: middle; }
.sdk table.profiler tr.mark td { padding: 0; }
.sdk table.profiler tr.mark.final td { padding: 0.2em 0.4em; }
.sdk table.profiler tr.mark td > div { position: relative; padding: 0.2em 0.4em; }
.sdk table.profiler tr.mark td div.value { position: relative; z-index: 2; }
.sdk table.profiler tr.mark td div.graph { position: absolute; top: 0; bottom: 0; right: 0; left: 100%; background: #71bdf0; z-index: 1; }
.sdk table.profiler tr.mark.memory td div.graph { background: #acd4f0; }
.sdk table.profiler tr.mark td.current { background: #eddecc; }
.sdk table.profiler tr.mark td.min { background: #d2f1cb; }
.sdk table.profiler tr.mark td.max { background: #ead3cb; }
.sdk table.profiler tr.mark td.average { background: #ddd; }
.sdk table.profiler tr.mark td.total { background: #d0e3f0; }
.sdk table.profiler tr.time td { border-bottom: 0; font-weight: bold; }
.sdk table.profiler tr.memory td { border-top: 0; }
.sdk table.profiler tr.final th.name { background: #222; color: #fff; }
.sdk table.profiler abbr { border: 0; color: #777; font-weight: normal; }
.sdk table.profiler:hover tr.group td { color: #ccc; }
.sdk table.profiler:hover tr.mark td div.graph { background: #1197f0; }
.sdk table.profiler:hover tr.mark.memory td div.graph { background: #7cc1f0; }
.sdk table.profiler tr.app td {border-right:1px solid #999}
</style>

<?php
$groupStats      = $profiler->groupStats();
$group_cols       = array('min', 'max', 'average', 'total');
?>

<div class="sdk">

	<table class="profiler">
		<tr class="group"><th class="name">$_GET</th><th></th></tr>
		<?php foreach($_GET as $key => $value) {
			echo '<tr><td class="mark" style="text-align:left;vertical-align:middle;width:25%;height:50px;font-size:20px">"'.$key.'"</td><td style="background:#ddd;text-align:left;vertical-align:middle">'.$value.'</td></tr>';
		} ?>
	</table>

	<table class="profiler">
		<tr class="group"><th class="name">$_POST</th><th></th></tr>
		<?php foreach($_POST as $key => $value) {
			echo '<tr><td class="mark" style="text-align:left;vertical-align:middle;width:25%;height:50px;font-size:20px">"'.$key.'"</td><td style="background:#ddd;text-align:left;vertical-align:middle">'.$value.'</td></tr>';
		} ?>
	</table>

	<table class="profiler">
		<tr class="group"><th class="name">$_COOKIE</th><th></th></tr>
		<?php foreach($_COOKIE as $key => $value) {
			echo '<tr><td class="mark" style="text-align:left;vertical-align:middle;width:25%;height:50px;font-size:20px">"'.$key.'"</td><td style="background:#ddd;text-align:left;vertical-align:middle">'.$value.'</td></tr>';
		} ?>
	</table>

	<?php foreach ($profiler->groups() as $group => $benchmarks): ?>
	<table class="profiler">
		<tr class="group">
			<th class="name" rowspan="2"><?php echo ucfirst($group) ?></th>
			<td class="time" colspan="4"><?php echo $groupStats[$group]['total']['time'] ?> <abbr title="seconds">s</abbr></td>
		</tr>
		<tr class="group">
			<td class="memory" colspan="4"><?php echo number_format($groupStats[$group]['total']['memory'] / 1024, 4) ?> <abbr title="kilobyte">kB</abbr></td>
		</tr>
		<tr class="headers">
			<th class="name"><?php echo 'Benchmark' ?></th>
			<?php foreach ($group_cols as $key): ?>
			<th class="<?php echo $key ?>"><?php echo ucfirst($key) ?></th>
			<?php endforeach ?>
		</tr>
		<?php foreach ($benchmarks as $name => $tokens): ?>
		<tr class="mark time">
			<?php $stats = $profiler->stats($tokens) ?>
			<th class="name" rowspan="2" scope="rowgroup"><?php echo $name, ' (', count($tokens), ')' ?></th>
			<?php foreach ($group_cols as $key): ?>
			<td class="<?php echo $key ?>">
				<div>
					<div class="value"><?php echo number_format($stats[$key]['time'], 6) ?> <abbr title="seconds">s</abbr></div>
					<?php if ($key === 'total'): ?>
						<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['time'] / $groupStats[$group]['max']['time'] * 100) ?>%"></div>
					<?php endif ?>
				</div>
			</td>
			<?php endforeach ?>
		</tr>
		<tr class="mark memory">
			<?php foreach ($group_cols as $key): ?>
			<td class="<?php echo $key ?>">
				<div>
					<div class="value"><?php echo number_format($stats[$key]['memory'] / 1024, 4) ?> <abbr title="kilobyte">kB</abbr></div>
					<?php if ($key === 'total'): ?>
						<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['memory'] / $groupStats[$group]['max']['memory'] * 100) ?>%"></div>
					<?php endif ?>
				</div>
			</td>
			<?php endforeach ?>
		</tr>
		<?php endforeach ?>
	</table>
	<?php endforeach ?>

	<table class="profiler">
		<?php $stats = $profiler->application() ?>
		<tr class="final mark time">
			<th class="name" rowspan="2" scope="rowgroup"><?php echo 'Application Execution' ?></th>
		</tr>
		<tr class="app">
			<?php 
			$time = $stats['time'];
			if(!is_string($time)) {
				$time = number_format($stats['time'], 6).'<abbr title="seconds">s</abbr>';
			}
			$memory = $stats['memory'];
			if(!is_string($memory)) {
				$memory = number_format($stats['memory'] / 1024, 4).'<abbr title="kilobyte">kB</abbr>';
			}
			?>
			<td class="time"><?php echo $time; ?> </td>
			<td class="memory"><?php echo $memory; ?> </td>
		</tr>
	</table>

</div>

<?php
	}
	//}}}
	
}
