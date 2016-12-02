.DELETE_ON_ERROR:

y.pdf:	y.tex
	rubber -m xelatex $<

y.tex:	replaceittex.php
	php $< HERREN +v +f +s > $@
