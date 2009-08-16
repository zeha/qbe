CFLAGS=-O3 -Wall

all: 
	@echo ""
	@echo "SAS: Building ..."
	@make build

build: sasusernote
	@strip sasusernote

sasusernote: sasusernote.c

clean:
	@echo "SAS: Cleaning up ..."
	@-rm sasusernote
	@-rm *~
	@-rm *.o