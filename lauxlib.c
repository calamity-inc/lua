/*
** $Id: lauxlib.c,v 1.19 1999/09/06 13:13:03 roberto Exp roberto $
** Auxiliary functions for building Lua libraries
** See Copyright Notice in lua.h
*/


#include <stdarg.h>
#include <stdio.h>
#include <string.h>

/* Please Notice: This file uses only the official API of Lua
** Any function declared here could be written as an application function.
** With care, these functions can be used by other libraries.
*/

#include "lauxlib.h"
#include "lua.h"
#include "luadebug.h"



int luaL_findstring (const char *name, const char *const list[]) {
  int i;
  for (i=0; list[i]; i++)
    if (strcmp(list[i], name) == 0)
      return i;
  return -1;  /* name not found */
}

void luaL_argerror (int numarg, const char *extramsg) {
  lua_Function f = lua_stackedfunction(0);
  const char *funcname;
  lua_getobjname(f, &funcname);
  numarg -= lua_nups(f);
  if (funcname == NULL)
    funcname = "?";
  luaL_verror("bad argument #%d to function `%.50s' (%.100s)",
              numarg, funcname, extramsg);
}

static const char *checkstr (lua_Object o, int numArg, long *len) {
  const char *s = lua_getstring(o);
  luaL_arg_check(s, numArg, "string expected");
  if (len) *len = lua_strlen(o);
  return s;
}

const char *luaL_check_lstr (int numArg, long *len) {
  return checkstr(lua_getparam(numArg), numArg, len);
}

const char *luaL_opt_lstr (int numArg, const char *def, long *len) {
  lua_Object o = lua_getparam(numArg);
  if (o == LUA_NOOBJECT) {
    if (len) *len = def ? strlen(def) : 0;
    return def;
  }
  else return checkstr(o, numArg, len);
}

double luaL_check_number (int numArg) {
  lua_Object o = lua_getparam(numArg);
  luaL_arg_check(lua_isnumber(o), numArg, "number expected");
  return lua_getnumber(o);
}


double luaL_opt_number (int numArg, double def) {
  lua_Object o = lua_getparam(numArg);
  if (o == LUA_NOOBJECT) return def;
  else {
    luaL_arg_check(lua_isnumber(o), numArg, "number expected");
    return lua_getnumber(o);
  }
}


lua_Object luaL_tablearg (int arg) {
  lua_Object o = lua_getparam(arg);
  luaL_arg_check(lua_istable(o), arg, "table expected");
  return o;
}

lua_Object luaL_functionarg (int arg) {
  lua_Object o = lua_getparam(arg);
  luaL_arg_check(lua_isfunction(o), arg, "function expected");
  return o;
}

lua_Object luaL_nonnullarg (int numArg) {
  lua_Object o = lua_getparam(numArg);
  luaL_arg_check(o != LUA_NOOBJECT, numArg, "value expected");
  return o;
}

void luaL_openlib (const struct luaL_reg *l, int n) {
  int i;
  lua_open();  /* make sure lua is already open */
  for (i=0; i<n; i++)
    lua_register(l[i].name, l[i].func);
}


void luaL_verror (const char *fmt, ...) {
  char buff[500];
  va_list argp;
  va_start(argp, fmt);
  vsprintf(buff, fmt, argp);
  va_end(argp);
  lua_error(buff);
}


void luaL_chunkid (char *out, const char *source, int len) {
  len -= 13;  /* 13 = strlen("string ''...\0") */
  if (*source == '@')
    sprintf(out, "file `%.*s'", len, source+1);
  else if (*source == '(')
    strcpy(out, "(C code)");
  else {
    const char *b = strchr(source , '\n');  /* stop string at first new line */
    int lim = (b && (b-source)<len) ? b-source : len;
    sprintf(out, "string `%.*s'", lim, source);
    strcpy(out+lim+(13-5), "...'");  /* 5 = strlen("...'\0") */
  }
}


void luaL_filesource (char *out, const char *filename, int len) {
  if (filename == NULL) filename = "(stdin)";
  sprintf(out, "@%.*s", len-2, filename);  /* -2 for '@' and '\0' */
}
