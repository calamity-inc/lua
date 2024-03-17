<?php
chdir(__DIR__."/.."); // Ensure working directory is repository root
mkdir("lua54");
file_put_contents("lua54/LICENSE.txt", <<<EOC
Copyright © 1994–2023 Lua.org, PUC-Rio.
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
EOC);
copy("lua.exe", "lua54/lua54.exe");
copy("luac.exe", "lua54/luac54.exe");
chdir("lua54");
file_put_contents(".chocogen.json", <<<EOC
{
    "path": ["lua54.exe", "luac54.exe", "LICENSE.txt", "VERIFICATION.txt"],
    "title": "Lua 5.4",
    "description": "Lua is a lightweight, high-level, multi-paradigm programming language. This package for version 5.4 provides lua54.exe and luac54.exe.",
    "authors": "Lua.org, PUC-Rio",
    "website": "https://lua.org/",
    "repository": "https://github.com/calamity-inc/lua",
    "tags": "lua development programming foss cross-platform non-admin",
    "icon": "https://lua.org/favicon.ico",
    "license": "https://lua.org/license.html",
    "changelog": "https://lua.org/versions.html",
    "issues": "https://lua.org/lua-l.html"
}
EOC);
file_put_contents("VERIFICATION.txt", "These are the same files as in the Windows.zip for this release: https://github.com/calamity-inc/lua/releases");
file_put_contents("chocogen.php", file_get_contents("https://raw.githubusercontent.com/calamity-inc/chocogen/0b1c843eaa167caf590bd56f46f5a57f6ffc6000/chocogen.php"));
passthru("php chocogen.php 5.4.6");
