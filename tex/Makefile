.DELETE_ON_ERROR:

.PHONY: force

../dfb.pdf:	force
	echo '******** HAR DU HUSKET AT RETTE DATO I replaceittex.pdf? ********'
	php replaceittex.php HERREN d +v +f +s +o > y.tex
	rubber -m xelatex y.tex
	cp y.pdf $@
