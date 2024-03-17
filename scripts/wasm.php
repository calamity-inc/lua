<?php
require "common.php";

$clang = "emcc -O3 -flto -fvisibility=hidden -D LUA_USE_LONGJMP";

// Setup folders
if(!is_dir("bin"))
{
	mkdir("bin");
}
if(!is_dir("bin/int"))
{
	mkdir("bin/int");
}

// Find work
$files = [];
foreach(scandir(".") as $file)
{
	if(substr($file, -2) == ".c")
	{
		$name = substr($file, 0, -2);
		if($name != "luac" && $name != "onelua")
		{
			array_push($files, $name);
		}
	}
}

echo "Compiling...\n";
$objects = [];
foreach($files as $file)
{
	run_command_async("$clang -c $file.c -o bin/int/".basename($file).".o");
	array_push($objects, escapeshellarg("bin/int/".basename($file).".o"));
}
await_commands();

echo "Linking lua...\n";
$link = $clang." -s WASM=1 -s MODULARIZE=1 -s EXPORT_NAME=lua -s EXPORTED_FUNCTIONS=_malloc,_main,_strcpy,_free -s EXPORTED_RUNTIME_METHODS=[\"FS\",\"cwrap\"] -s FS_DEBUG=1 -s FETCH=1";
$link .= " -s ALLOW_MEMORY_GROWTH=1 -s ABORTING_MALLOC=0"; // to correctly handle memory-intensive tasks
//$link .= " -s LINKABLE=1 -s EXPORT_ALL=1 -s ASSERTIONS=1"; // uncomment for debugging
passthru("$link -o lua.js ".join(" ", $objects));
