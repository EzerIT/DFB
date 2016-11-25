<?php

require_once('../oversigt.inc.php');

$include_status = array('btn-warning','btn-info','btn-success');

function replaceittex($filename, $chtype/*, &$title*/) {
    $txt = file_get_contents($filename);

    if (preg_match('/"/',$txt)) {
        echo "FEJL\n";
        echo "Tekst indeholder dobbelt citationstegn.\n";
        die;
    }

    $from[] = '/(!!<.*>!!)/';
    $to[] = '%\1'."\n";

    $from[] = '/===.*(apitel|alme) (.*){E:\s*([^}]+)}===/';
    $to[] = '\needspace{7\baselineskip}\section[' . $chtype . ' \2]{' . $chtype. ' \2\pagenote{\3}Z1Z}';

    $from[] = '/===.*(apitel|alme) (.*){T:\s*([^}]+)}===/';
    $to[] = '\needspace{7\baselineskip}\section[' . $chtype . ' \2]{' . $chtype. ' \2\footnote{\3}Z1Z}'; // Extra space needed here

    $from[] = '/===.*(apitel|alme) (.*)===/';
    $to[] = '\needspace{6\baselineskip}\section{' . $chtype . ' \2}';

    $from[] = '/==/';
    $to[] = '@';

    $from[] = '/>>>/';
    $to[] = '»›';

    $from[] = '/<<</';
    $to[] = '‹«';

    $from[] = '/>>/';
    $to[] = '»';

    $from[] = '/<</';
    $to[] = '«';

    $from[] = '/>/';
    $to[] = '›';

    $from[] = '/</';
    $to[] = '‹';

    $from[] = '/\*\*\*/';
    $to[] = '\\vspace{\\baselineskip}';

    $from[] = '/\*([^\*]+)\*/';
    $to[] = '\\emph{\1}';

    $from[] = '/\s*{E:\s*([^}]+)}/';
    $to[] = '\\pagenote{\1}Z1Z';

    $from[] = '/\s*{T:\s*([^}]+)}/';
    $to[] = '\\footnote{\1}Z1Z';

    $from[] = '/(JHVH[sv]*)/i';
    $to[] = 'BACKSLASH\1{}';

    $from[] = '/BACKSLASH/';
    $to[] = '\\';

    $from[] = '/([^a-z])v([0-9]+)[\n ]*/';
    $to[] = '\1\\bverse{\2}';

    $from[] = '/\n *\n/';
    $to[] = 'QQ';

    $from[] = '/\n/';
    $to[] = ' ';

    $from[] = '/QQ/';
    $to[] = "\n\n";

    $from[] = '/\*\*\*/';
    $to[] = '\\pfbreak';

    $from[] = '/(\s)-(\s)/';
    $to[] = '\1 -- \2';

    $from[] = '/\.\.\./';
    $to[] = '…';  // \ldots ?

    $txt = preg_replace($from, $to, $txt);

    $txt = preg_replace_callback('/@([^@]+)@/',  // Handles footnotes within headings
                                 function ($matches) {
                                     if (preg_match('/(.*)(.footnote.*)Z1Z(.*)/',
                                                    $matches[1],
                                                    $m)) {
                                         return '\needspace{1\baselineskip}\subsection[' . $m[1] . $m[3] . ']{' . $m[0] . '}';
                                     }
                                     return '\needspace{1\baselineskip}\subsection{' . $matches[1] . '}';
                                 }, $txt);


    $txt = preg_replace('/Z1Z/', '', $txt);

    return  $txt;
  }

function fix_hyphenation($txt) {
    $from[] = '/([Kk])ana\'anæerne/';
    $to[] = '\1a\-na\'\-a\-næ\-er\-ne';

    $from[] = '/([Bb])a\'alerne/';
    $to[] = '\1a\'\-a\-ler\-ne';

    $from[] = '/([Vv])agtposterne/';
    $to[] = '\1agt\-post\-er\-ne';

    $from[] = '/spændte/';
    $to[] = 'spænd\-te';

    return preg_replace($from, $to, $txt);
}




echo<<<'END'

\documentclass[11pt,oneside,a4paper]{memoir}
\usepackage{fontspec}
\usepackage{xcolor}
\usepackage{polyglossia}

\setdefaultlanguage{danish}

%\setmainfont[Ligatures=TeX]{Times New Roman}
\setmainfont[Ligatures=TeX]{DejaVu Serif Condensed}
%\setmainfont[Ligatures=TeX]{DejaVu Serif}
%\setmainfont[Ligatures=TeX]{Junicode}

\setsecnumdepth{book}
\newcommand{\bverse}[1]{{\scriptsize\color{red} $^{#1}$}\ignorespaces}

\renewcommand{\thepagenote}{\alph{pagenote}}

\makeoddhead{ruled}{\scshape\leftmark}{}{\rightmark}
\makeoddfoot{plain}{}{}{\thepage}

\renewcommand\pagenotesubhead[3]{}  % No section heading in exegetical notes
\renewcommand\pagenotesubheadstarred[3]{}  % No section heading in exegetical notes

\settocdepth{chapter}

%%% Allow extra space between words %%%
\sloppy

\makepagenote
\renewcommand*{\notedivision}{\clearpage\section{Eksegetiske noter}}

\makeatletter
\renewcommand*{\@alph}[1]{%    Define what \alph means
  \ifcase#1%
      \or a\or b\or c\or d\or e\or f\or g\or h\or i\or j%
      \or k\or l\or m\or n\or o\or p\or q\or r\or s\or t%
      \or u\or v\or w\or x\or y\or z\or æ\or ø\or å%
      \or aa\or ab\or ac\or ad\or ae\or af\or ag\or ah\or ai\or aj%
      \or ak\or al\or am\or an\or ao\or ap\or aq\or ar\or as\or at%
      \or au\or av\or aw\or ax\or ay\or az\or aæ\or aø\or aå%
      \or ba\or bb\or bc\or bd\or be\or bf\or bg\or bh\or bi\or bj%
      \or bk\or bl\or bm\or bn\or bo\or bp\or bq\or br\or bs\or bt%
      \or bu\or bv\or bw\or bx\or by\or bz\or bæ\or bø\or bå%
      \or ca\or cb\or cc\or cd\or ce\or cf\or cg\or ch\or ci\or cj%
      \or ck\or cl\or cm\or cn\or co\or cp\or cq\or cr\or cs\or ct%
      \or cu\or cv\or cw\or cx\or cy\or cz\or cæ\or cø\or cå%
      \or da\or db\or dc\or dd\or de\or df\or dg\or dh\or di\or dj%
      \or dk\or dl\or dm\or dn\or do\or dp\or dq\or dr\or ds\or dt%
      \or du\or dv\or dw\or dx\or dy\or dz\or dæ\or dø\or då%
      \or ea\or eb\or ec\or ed\or ee\or ef\or eg\or eh\or ei\or ej%
      \or ek\or el\or em\or en\or eo\or ep\or eq\or er\or es\or et%
      \or eu\or ev\or ew\or ex\or ey\or ez\or eæ\or eø\or eå%
      \or fa\or fb\or fc\or fd\or fe\or ff\or fg\or fh\or fi\or fj%
      \or fk\or fl\or fm\or fn\or fo\or fp\or fq\or fr\or fs\or ft%
      \or fu\or fv\or fw\or fx\or fy\or fz\or fæ\or fø\or få%
      \or ga\or gb\or gc\or gd\or ge\or gf\or gg\or gh\or gi\or gj%
      \or gk\or gl\or gm\or gn\or go\or gp\or gq\or gr\or gs\or gt%
      \or gu\or gv\or gw\or gx\or gy\or gz\or gæ\or gø\or gå%
      \or ha\or hb\or hc\or hd\or he\or hf\or hg\or hh\or hi\or hj%
      \or hk\or hl\or hm\or hn\or ho\or hp\or hq\or hr\or hs\or ht%
      \or hu\or hv\or hw\or hx\or hy\or hz\or hæ\or hø\or hå%
      \else\@ctrerr\fi
}
\makeatother



\newcommand{\JHVH}{H{\footnotesize{ERREN}}}
\newcommand{\JHVHs}{H{\footnotesize{ERRENS}}}
\newcommand{\JHVHv}{H{\footnotesize{ERRE}}}
\newcommand{\JHVHvs}{H{\footnotesize{ERRES}}}

\pagestyle{ruled}
\aliaspagestyle{part}{empty}

\renewcommand{\cftchapterfont}{} % Use normal font for chapter name in TOC
\renewcommand{\cftchapterpagefont}{} % Use normal font for chapter page number in TOC

\setsubsecheadstyle{\itshape}


\title{Den Frie Bibel}
\date{25.12.2016}


\begin{document}
\begin{titlingpage*}
\maketitle
\end{titlingpage*}

\markright{}
\tableofcontents*

\chapter[Forord]{Fordord}\setcounter{footnote}{0}\setcounter{pagenote}{0}
\input{forord.tex}%


END;

foreach ($title as $bookabb => $tit) {
    if ($bookabb=='GT' || $bookabb=='NT') {
        if (in_array($style[$bookabb],$include_status))
            echo '\part{' . $tit . "}\n\n";
        continue;
    }

    if (is_array($style[$bookabb]) || in_array($style[$bookabb],$include_status)) {
        $text = '';
        foreach ($chap[$bookabb] as $ch) {
            if (is_array($style[$bookabb]) && in_array($style[$bookabb][$ch],$include_status)
                || in_array($style[$bookabb],$include_status)) {
                $text .= replaceittex(sprintf('../tekst/%s%03d.txt',$bookabb,$ch), ucfirst($chaptype[$bookabb]));
            }
        }
        $text = fix_hyphenation($text);

        $title[$bookabb] = preg_replace('/&rsquo;/',"'",$title[$bookabb]);

        echo '\chapter[' . $title[$bookabb] . ']{' . $title[$bookabb] . "}\setcounter{footnote}{0}\setcounter{pagenote}{0}\n";
        echo $text;
        echo "\\printpagenotes*\n";
    }
}


echo "\\end{document}\n";