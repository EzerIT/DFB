.DELETE_ON_ERROR:

.PHONY: force

y.pdf:	force
	php replaceittex.php HERREN +v +f +s > y.tex
	rubber -m xelatex y.tex
