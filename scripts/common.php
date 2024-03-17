<?php
chdir(__DIR__."/.."); // Ensure working directory is repository root

if(!is_dir("int"))
{
	mkdir("int");
}

function check_compiler()
{
	global $argv, $compiler;

	if(empty($argv[1]))
	{
		die("Syntax: php {$argv[0]} <compiler>");
	}

	$compiler = resolve_installed_program($argv[1]);
	$compiler .= " -O3 -fvisibility=hidden";
	if(defined("PHP_WINDOWS_VERSION_MAJOR"))
	{
		$compiler .= " -D _CRT_SECURE_NO_WARNINGS";
	}
}

function resolve_installed_program($exe)
{
	if(defined("PHP_WINDOWS_VERSION_MAJOR"))
	{
		return escapeshellarg(system("where ".escapeshellarg($exe)));
	}
	return escapeshellarg(system("which ".escapeshellarg($exe)));
}

function for_each_obj($f)
{
	foreach(scandir(".") as $file)
	{
		if(substr($file, -2) == ".c")
		{
			$f(substr($file, 0, -2));
		}
	}
}

$procs = [];

function run_command_async($cmd)
{
	global $procs;
	echo ".";
	$file = tmpfile();
	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("file", stream_get_meta_data($file)["uri"], "a"),
		2 => array("file", stream_get_meta_data($file)["uri"], "a"),
	);
	$proc = proc_open($cmd, $descriptorspec, $pipes);
	array_push($procs, [ $proc, $file ]);
	if (count($procs) > 32)
	{
		await_commands();
	}
}

function await_commands()
{
	global $procs;
	echo "\r";
	$output = "";
	while (count($procs) != 0)
	{
		foreach ($procs as $i => $proc)
		{
			if (!proc_get_status($proc[0])["running"])
			{
				echo "█";
				$output .= stream_get_contents($proc[1]);
				fclose($proc[1]);
				proc_close($proc[0]);
				unset($procs[$i]);
			}
		}
		usleep(50000);
	}
	echo "\n";
	echo $output;
}
