#
# Makefile
# for Qbe SAS SystemDocumentation
#
# use "make" (or "make pdf") to recompile the doc.
# use "make all" to recreate all the doxygen stuff from scratch and recompile the doc.
# use "make htl" for the brain-dead htl formatting of the doc.
#

# primary targets

# master target
pdf:	QbeSASDoc.pdf

htl:	htl-master.pdf

all:	clean doxygen doxypatch pdf

upload:	QbeSASDoc.pdf htl-master.pdf
	scp QbeSASDoc.pdf htl-master.pdf ch@sas.qbe.ch:/kunden/vhosts/qbe.ch/sas/htdocs/sysdoc/

# file targets
QbeSASDoc.pdf: *.tex
	pdflatex QbeSASDoc.tex
	makeindex QbeSASDoc.idx
	pdflatex QbeSASDoc.tex

	latex_count=5 ; \
	while egrep -s 'Rerun (LaTeX|to get cross-references right)' QbeSASDoc.log && [ $$latex_count -gt 0 ] ;\
	do \
	  echo "Rerunning latex...." ;\
	  pdflatex QbeSASDoc.tex ;\
	  latex_count=`expr $$latex_count - 1` ;\
	done

htl-master.pdf: *.tex
	pdflatex htl-master.tex
	makeindex htl-master.idx
	pdflatex htl-master.tex

doxygen:
	rm -f latex/*
	doxygen

doxypatch:
	rm -f doxy/*
	cd latex; for i in *; do sed -e 's/verbatim/lstlisting/' $$i > ../doxy/$$i; done; cd ..

#xref.tex: latex/*.tex
#	cd latex; ls -1 *.tex | perl -e 'while (<STDIN>) { $_=~s/\n//; print "\\input{latex/$_}\n"; }'

distclean:
	rm -f *.ps *.dvi *.aux *.toc *.idx *.ind *.ilg *.log *.out QbeSASDoc.pdf htl-master.pdf

clean:
	rm -f *.toc *.idx *.ind *.ilg *.log *.lof QbeSASDoc.pdf htl-master.pdf

