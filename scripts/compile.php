<?php
require __DIR__."/common.php";
check_compiler();

for_each_obj(function($file)
{
	if ($file != "onelua")
	{
		global $compiler;
		run_command_async($compiler." -o int/{$file}.o -c {$file}.c");
	}
});
await_commands();
