#
# Must specify target link file on the command line, i.e.:
#
#     make all LINKFILE=win32lnk.o
#     make atodtst LINKFILE=linuxlnk.o
#     make temp LINKFILE=myWeirdOSlnk.c
#
# or, call make with either of the following two targets
# specified, 'win32' or 'linux':
#
#     make win32
#     make linux
#
# This will cause the make file to use the default link
# file for the target platform and build all applications.
#

CC = gcc
FLAGS = -DDEBUG -g -o $@

ONEWIREOBJS = $(LINKFILE) ds2480ut.o ownetu.o owllu.o owsesu.o owtrnu.o \
	owerr.o ioutil.o crcutil.o
WEATHEROBJS = weather.o atod20.o cnt1d.o swt12.o temp10.o $(ONEWIREOBJS)
MEMOBJS = mbappreg.o mbeprom.o mbnvcrc.o mbscrcrc.o mbscrex.o mbshaee.o \
	mbee.o mbnv.o mbscr.o mbscree.o mbsha.o rawmem.o $(ONEWIREOBJS)
FILEOBJS = owfile.o owpgrw.o owprgm.o owcache.o $(MEMOBJS)
SHAOBJS = sha18.o sha33.o shaib.o $(FILEOBJS)
PROGS = atodtst counter coupler fish gethumd memutil mweather shaapp swtloop \
	swtsngl temp thermodl thermoms tstfind debit debitvm initcopr \
	initcoprvm initrov initrovvm jibtest jibload jibmodpow ps_check ps_init \
	sha_chck sha_init tm_check tm_init


win32:
	make all LINKFILE=win32lnk.o

linux:
	make all LINKFILE=linuxlnk.o

all: $(PROGS)


atodtst:  atodtst.c atod20.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) atodtst.c atod20.o findtype.o $(ONEWIREOBJS)

counter:  counter.c cnt1d.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) counter.c cnt1d.o findtype.o $(ONEWIREOBJS)

coupler: coupler.c swt1f.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) coupler.c swt1f.o findtype.o $(ONEWIREOBJS)

debit: shademo.c shadebit.o $(SHAOBJS)
	$(CC) $(FLAGS) shademo.c shadebit.o $(SHAOBJS)

debitvm: shademo.c shadbtvm.o $(SHAOBJS)
	$(CC) -DCOPRVM $(FLAGS) shademo.c shadbtvm.o $(SHAOBJS)

fish: fish.c $(FILEOBJS)
	$(CC) $(FLAGS) fish.c $(FILEOBJS)

gethumd: gethumd.c atod26.o screenio.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) gethumd.c atod26.o screenio.o findtype.o $(ONEWIREOBJS)

initcopr: shacopr.c shadebit.o $(SHAOBJS)
	$(CC) $(FLAGS) shacopr.c shadebit.o $(SHAOBJS)

initcoprvm: shacopr.c shadbtvm.o $(SHAOBJS)
	$(CC) -DCOPRVM $(FLAGS) shacopr.c shadbtvm.o $(SHAOBJS)

initrov: sharov.c shadebit.o $(SHAOBJS)
	$(CC) $(FLAGS) sharov.c shadebit.o $(SHAOBJS)

initrovvm: sharov.c shadbtvm.o $(SHAOBJS)
	$(CC) -DCOPRVM $(FLAGS) sharov.c shadbtvm.o $(SHAOBJS)

jibload: jibload.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) jibload.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)

jibmodpow: jibmodpw.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) jibmodpw.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)

jibtest: jibtest.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) jibtest.c jib96.o jib96o.o findtype.o $(ONEWIREOBJS)

memutil: memutil.c $(FILEOBJS)
	$(CC) $(FLAGS) memutil.c $(FILEOBJS)

mweather: mweather.c findtype.o $(WEATHEROBJS)
	$(CC) $(FLAGS) mweather.c findtype.o $(WEATHEROBJS)

ps_check: ps_check.c ps02.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) ps_check.c ps02.o findtype.o $(ONEWIREOBJS)
	
ps_init: ps_init.c ps02.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) ps_init.c ps02.o findtype.o $(ONEWIREOBJS)
	
sha_chck: sha_chck.c shadbtvm.o $(SHAOBJS)
	$(CC) $(FLAGS) sha_chck.c shadbtvm.o $(SHAOBJS)
	
sha_init: sha_init.c shadbtvm.o $(SHAOBJS)
	$(CC) $(FLAGS) sha_init.c shadbtvm.o $(SHAOBJS)
	
shaapp: shaapp.c ibsha33o.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) shaapp.c ibsha33o.o $(ONEWIREOBJS)

swtloop: swtloop.c swt12.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) swtloop.c swt12.o findtype.o $(ONEWIREOBJS)

swtsngl: swtsngl.c swt05.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) swtsngl.c swt05.o findtype.o $(ONEWIREOBJS)

temp:  temp.c temp10.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) temp.c temp10.o findtype.o $(ONEWIREOBJS)

thermodl: thermodl.c thermo21.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) thermodl.c thermo21.o findtype.o $(ONEWIREOBJS)

thermoms: thermoms.c thermo21.o findtype.o $(ONEWIREOBJS)
	$(CC) $(FLAGS) thermoms.c thermo21.o findtype.o $(ONEWIREOBJS)

tm_check: tm_check.c time04.o findtype.o $(FILEOBJS)
	$(CC) $(FLAGS) tm_check.c time04.o findtype.o $(FILEOBJS)

tm_init: tm_init.c time04.o findtype.o $(FILEOBJS)
	$(CC) $(FLAGS) tm_init.c time04.o findtype.o $(FILEOBJS)

tstfind: tstfind.c $(ONEWIREOBJS)
	$(CC) $(FLAGS) tstfind.c $(ONEWIREOBJS)

%.o: %.c
	$(CC) -c -DDEBUG $<

clean:
	rm -f *.o *.exe $(PROGS)

