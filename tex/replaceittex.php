<?php

require_once('../oversigt.inc.php');

function replaceittex($filename, $chtype/*, &$title*/) {
    $txt = file_get_contents($filename);

    if (preg_match('/"/',$txt)) {
        echo "FEJL\n";
        echo "Tekst indeholder dobbelt citationstegn.\n";
        die;
    }

    $from[] = '/(!!<.*>!!)/';
    $to[] = '%\1'."\n";

    $from[] = '/===.*(apitel|alme) (.*)===\s*{E:\s*([^}]+)}/';
    $to[] = '\needspace{5\baselineskip}\section[' . $chtype . ' \2]{' . $chtype. ' \2\pagenote{\3}Z1Z}';

    $from[] = '/===.*(apitel|alme) (.*)===\s*{T:\s*([^}]+)}/';
    $to[] = '\needspace{7\baselineskip}\section[' . $chtype . ' \2]{' . $chtype. ' \2\footnote{\3}Z1Z}'; // Extra space needed here

    $from[] = '/===.*(apitel|alme) (.*)===/';
    $to[] = '\needspace{5\baselineskip}\section{' . $chtype . ' \2}';

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

\documentclass[11pt,oneside,a4paper,draft]{memoir}
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
\date{02.09.2016}


\begin{document}
\begin{titlingpage*}
\maketitle
\end{titlingpage*}

\markright{}
\tableofcontents*

\chapter[Forord]{Fordord}\setcounter{footnote}{0}\setcounter{pagenote}{0}
\input{forord.tex}%


\part{Det Gamle Testamente}



END;

foreach ($chap as $bookabb => $chapters) {
    if (is_array($style[$bookabb]) || $style[$bookabb]==='btn-success') {
        $text = '';
        foreach ($chapters as $ch) {
            if (is_array($style[$bookabb]) && $style[$bookabb][$ch]==='btn-success' || $style[$bookabb]==='btn-success') {
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