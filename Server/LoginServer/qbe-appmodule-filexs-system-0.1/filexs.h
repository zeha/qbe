/*
 * FileXS Helper
 *
 * Copyright 2003 Christian Hofstaedtler
 */

#define ADMIN_UID 500
#define ADMIN_GID 150

#define PARAM_PROG 0
#define PARAM_USER 1
#define PARAM_GROUP 2
#define PARAM_ACTION 3
#define PARAMCOUNT 4

#define ERROR_SUCCESS 0
#define ERROR_ARGCOUNT 1
#define ERROR_USERGROUP 2

#define ACTION_FILEGET "fileget"
#define ACTION_FILEPUT "fileput"
#define ACTION_RENAME "rename"
#define ACTION_DELETE "delete"
#define ACTION_MKDIR "mkdir"
#define ACTION_RMDIR "rmdir"

extern int filexs_fileget(int argc, char** argv);
extern int filexs_fileput(int argc, char** argv);
extern int filexs_rename(int argc, char** argv);
extern int filexs_delete(int argc, char** argv);
extern int filexs_mkdir(int argc, char** argv);
extern int filexs_rmdir(int argc, char** argv);

// --- eof ---

