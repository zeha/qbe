#include "ownet.h"
#include "shaib.h"

extern SMALLINT owFirst(int,SMALLINT,SMALLINT);
extern SMALLINT owAcquire(int,char *);
extern void owRelease(int);
extern long msGettick();

static int GetSecret(char* name, uchar** secret);

int main(int argc, char** argv)
{
   int i = 0;
   SHACopr copr;
   SHAUser user;
   FileEntry fe = {"COPR",0};
   uchar *authSecret;
   int authlen;
   char test[2] = {'y',0};
   long balance;

   copr.portnum = 0;
   user.portnum = 0;

   puts("\nStarting SHA initrov Application\n");

   // check for required port name
   if (argc != 2)
   {
      printf("1-Wire Net name required on command line!\n"
             " (example: \"COM1\" (Win32 DS2480),\"/dev/cua0\" "
             "(Linux DS2480),\"1\" (Win32 TMEX)\n");
      exit(1);
   }

   if(!owAcquire(copr.portnum,argv[1]))
   {
      printf("Failed to acquire port.\n");
      exit(2);
   }

#ifdef COPRVM
   if(!GetCoprVM(&copr, &fe))
      exit(1);
#else
   puts("\nPlease place coprocessor token on the 1-Wire bus.\n");

   while(!FindCoprSHA(&copr, &fe))
   {
      if(owHasErrors())
         msDelay(10);
   }

   printf("Found device: ");
   PrintSerialNum(copr.devAN);
   puts("\n");
#endif

   authlen = GetSecret("System Authentication Secret", &authSecret);

   EnterString("Reformat the secret for DS1961S compatibility", test, 1, 1);
   if(test[0] == 'y')
   {
      ReformatSecretFor1961S(authSecret, authlen);
      PrintHex(authSecret, authlen);
      printf("\n");
      copr.ds1961Scompatible = 0x55;
   }

   puts("\nPlease place user token on the 1-Wire bus.\n");
   do
   {
      while(!FindNewSHA(user.portnum, user.devAN, (i==0)))
      {
         if(owHasErrors())
            msDelay(10);
      }
      i++;
   }
   while(user.devAN[7]==copr.devAN[7]);
   // just check the crc of the two devices

   balance = 100;
   EnterNum("\nInitial Balance in Cents?\n", 5, &balance, 0, 0xFFFFF);

   printf("Installing Service Data on device: ");
   PrintSerialNum(user.devAN);
   puts("\n");
   if(InstallServiceData(&copr, &user, authSecret, authlen, (int)balance))
   {
      puts("User token successfully set up");
   }
   else
   {
      puts("User token setup failed");
      OWERROR_DUMP(stdout);
   }

   // and we're done
   owRelease(copr.portnum);

   // program is about to exit, but we may as well free these
   // up anyways...
   free(authSecret);

   return 0;
}


static int GetSecret(char* name, uchar** secret)
{
   uchar inputBuffer[255];
   long lvalue=1, length;

   printf("How would you like to enter the %s?\n", name);
   EnterNum("\n   1) Hex\n   2) Text\n", 1, &lvalue, 1, 2);

   lvalue = getData(inputBuffer, 255, (lvalue==1));

   if(lvalue%47!=0)
      length = ((lvalue/47) + 1)*47;
   else
      length = lvalue;
   *secret = malloc(length);
   memset(*secret, 0x00, length);
   memcpy(*secret, inputBuffer, lvalue);

   printf("length=%ld\n",length);
   PrintHex(*secret, length);
   printf("\n");

   return length;
}



