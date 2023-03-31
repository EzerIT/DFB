.DELETE_ON_ERROR:

.PHONY: force

../dfb.pdf:	force
	php replaceittex.php HERREN d +v +f +s +o > y.tex
	rubber -m xelatex y.tex
	cp y.pdf $@
