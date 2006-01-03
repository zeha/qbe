// Our .NET visible classes

#include "stdafx.h"

#using <mscorlib.dll>
using namespace System;

namespace QbeSAS 
{
	public __gc __sealed class RunInteractiveApplication
	{
	private:
		// private constructor to statisfy FxCop
		RunInteractiveApplication()
		{
		}
	public:
		static System::Boolean StartApplication(System::String* applicationImage)
		{
			System::String* password;
			double t = System::DateTime::Now.ToOADate();
			password = "Q";
			password->Concat(t.ToString());

			char app_utf8 __gc[] = System::Text::Encoding::UTF8->GetBytes(applicationImage);
			char __pin * app_str = &app_utf8[0];
			
			qbe_sam_createuser(L"qbehelper",L"predefinedpassword");

			qbe_nt_launchapp("qbehelper","predefinedpassword",app_str);

			qbe_sam_deleteuser(L"qbehelper");
			
			return true;
		}
	};
}
