.DELETE_ON_ERROR:

.PHONY: force

../dfb.pdf:	force
	php replaceittex.php HERREN d +v +f +s +o > y.tex
	latexmk -quiet y.tex
	cp y.pdf $@
