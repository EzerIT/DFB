.DELETE_ON_ERROR:

.PHONY: force

../dfb.pdf:	force
	php replaceittex.php HERREN +v +f +s > y.tex
	rubber -m xelatex y.tex
	cp y.pdf $@
