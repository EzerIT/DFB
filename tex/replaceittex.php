<?php

require_once('../oversigt.inc.php');

$include_status = array(/*'btn-warning',*/'btn-info','btn-success');

function replaceittex($filename, $chtype) {
    $txt = file_get_contents($filename);

    if (preg_match('/"/',$txt)) {
        echo "FEJL\n";
        echo "Tekst indeholder dobbelt citationstegn.\n";
        exit(2);
    }

    $from[] = '/(!!<.*>!!)/';
    $to[] = '%\1'."\n";

    $from[] = '/===.*(apitel|alme) (.*){E:\s*([^}]+)}===/';
    $to[] = '\needspace{7\baselineskip}\bchap{\2}\section[' . $chtype . ' \2]{' . $chtype. ' \2\pnotehead{\3}}';

    $from[] = '/===.*(apitel|alme) (.*){T:\s*([^}]+)}===/';
    $to[] = '\needspace{7\baselineskip}\bchap{\2}\section[' . $chtype . ' \2]{' . $chtype. ' \2\footnote{\3}Z1Z}';

    $from[] = '/===.*(apitel|alme) (.*)===/';
    $to[] = '\needspace{6\baselineskip}\bchap{\2}\section{' . $chtype . ' \2}';

    $from[] = '/^\s*==([^=]*){E:([^=]*)==\s*$/m'; // An "E:" footnote in a header4
    $to[] = '==\1{Q:\2==';

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
    $to[] = '\\pnote{\1}';

    $from[] = '/\s*{Q:\s*([^}]+)}/';
    $to[] = '\\pnotehead{\1}';

    $from[] = '/\s*{T:\s*([^}]+)}/';
    $to[] = '\\footnote{\1}Z1Z';

    $from[] = '/(JHVH[sv]*)/i';
    $to[] = 'BACKSLASH\1{}';

    $from[] = '/BACKSLASH/';
    $to[] = '\\';

    $from[] = '/HERRENS/';
    $to[] = '\\JHVHs{}';

    $from[] = '/HERRES/';
    $to[] = '\\JHVHvs{}';

    $from[] = '/HERRE/';
    $to[] = '\\JHVHv{}';

    $from[] = '/HERREN/';
    $to[] = '\\JHVH{}';
    
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

if ($_SERVER['argc']!=5
    || !in_array($_SERVER['argv'][1], array('Jahve', 'JHVH', 'HERREN', 'Herren'))
    || !in_array($_SERVER['argv'][2], array('+v', '-v'))
    || !in_array($_SERVER['argv'][3], array('+f', '-f'))
    || !in_array($_SERVER['argv'][4], array('+s', '-s'))
    ) {
    fwrite(STDERR,"brug: php replaceittex.php <HaShem> [+|-]v [+|-]f [+|-]s\n"
                . "      hvor <HaShem> er 'Jahve', 'JHVH', 'HERREN' eller 'Herren'\n"
                . "      +/-v står for med/uden versnumre\n"
                . "      +/-f står for med/uden fodnoter\n"
                . "      +/-s står for med/uden slutnoter\n");
    exit(1);
}

$HaShem = $_SERVER['argv'][1];

$with_verses = $_SERVER['argv'][2] == '+v';
$with_footnotes = $_SERVER['argv'][3] == '+f';
$with_endnotes = $_SERVER['argv'][4] == '+s';

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

\newcommand{\bibbook}{}   % Current book
\newcommand{\bibchap}{}   % Current chapter

\newcommand{\bchap}[1]{%
    \renewcommand{\bibchap}{#1}%
    \edef\posit{\bibbook\ \bibchap}%
    \edef\posithead{\bibbook\ \bibchap}%
}

END;

if ($with_verses) {
    echo '\newcommand{\bibverse}{}  % Current verse',"\n";
    echo '\newcommand{\bverse}[1]{%',"\n";
    echo '    \renewcommand{\bibverse}{#1}%',"\n";
    echo '    \edef\posit{\bibbook\ \bibchap,\bibverse}%',"\n";
    echo '    \edef\posithead{\bibbook\ \bibchap}%',"\n";
    echo '    {\scriptsize\color{red} $^{#1}$}\ignorespaces%',"\n";
    echo '}',"\n";
}
else
    echo '\newcommand{\bverse}[1]{}',"\n";

if (!$with_footnotes)
    echo '\renewcommand{\footnote}[1]{}',"\n";

if ($with_endnotes) {
    echo '% \expandafter is required here to ensure that \posit is expanded at the point of invocation',"\n";
    echo '\newcommand{\pnote}[1]{\expandafter\pagenote\expandafter{\expandafter[\posit]\ #1}}',"\n";
    echo '\newcommand{\pnotehead}[1]{\expandafter\pagenote\expandafter{\expandafter[\posithead]\ #1}}',"\n";
    echo "\n";
    echo '\renewcommand{\thepagenote}{\alph{pagenote}}',"\n";
    echo "\n";
    echo '\renewcommand\pagenotesubhead[3]{}  % No section heading in exegetical notes',"\n";
    echo '\renewcommand\pagenotesubheadstarred[3]{}  % No section heading in exegetical notes',"\n";
    echo "\n";
    echo '\makepagenote',"\n";
    echo '\renewcommand*{\notedivision}{\clearpage\section{Eksegetiske noter}}',"\n";
    echo "\n";
    echo '\makeatletter',"\n";
    echo '\renewcommand*{\@alph}[1]{%    Define what \alph means',"\n";
    echo '  \ifcase#1%',"\n";
    echo '      \or a\or b\or c\or d\or e\or f\or g\or h\or i\or j%',"\n";
    echo '      \or k\or l\or m\or n\or o\or p\or q\or r\or s\or t%',"\n";
    echo '      \or u\or v\or w\or x\or y\or z\or æ\or ø\or å%',"\n";
    echo '      \or aa\or ab\or ac\or ad\or ae\or af\or ag\or ah\or ai\or aj%',"\n";
    echo '      \or ak\or al\or am\or an\or ao\or ap\or aq\or ar\or as\or at%',"\n";
    echo '      \or au\or av\or aw\or ax\or ay\or az\or aæ\or aø\or aå%',"\n";
    echo '      \or ba\or bb\or bc\or bd\or be\or bf\or bg\or bh\or bi\or bj%',"\n";
    echo '      \or bk\or bl\or bm\or bn\or bo\or bp\or bq\or br\or bs\or bt%',"\n";
    echo '      \or bu\or bv\or bw\or bx\or by\or bz\or bæ\or bø\or bå%',"\n";
    echo '      \or ca\or cb\or cc\or cd\or ce\or cf\or cg\or ch\or ci\or cj%',"\n";
    echo '      \or ck\or cl\or cm\or cn\or co\or cp\or cq\or cr\or cs\or ct%',"\n";
    echo '      \or cu\or cv\or cw\or cx\or cy\or cz\or cæ\or cø\or cå%',"\n";
    echo '      \or da\or db\or dc\or dd\or de\or df\or dg\or dh\or di\or dj%',"\n";
    echo '      \or dk\or dl\or dm\or dn\or do\or dp\or dq\or dr\or ds\or dt%',"\n";
    echo '      \or du\or dv\or dw\or dx\or dy\or dz\or dæ\or dø\or då%',"\n";
    echo '      \or ea\or eb\or ec\or ed\or ee\or ef\or eg\or eh\or ei\or ej%',"\n";
    echo '      \or ek\or el\or em\or en\or eo\or ep\or eq\or er\or es\or et%',"\n";
    echo '      \or eu\or ev\or ew\or ex\or ey\or ez\or eæ\or eø\or eå%',"\n";
    echo '      \or fa\or fb\or fc\or fd\or fe\or ff\or fg\or fh\or fi\or fj%',"\n";
    echo '      \or fk\or fl\or fm\or fn\or fo\or fp\or fq\or fr\or fs\or ft%',"\n";
    echo '      \or fu\or fv\or fw\or fx\or fy\or fz\or fæ\or fø\or få%',"\n";
    echo '      \or ga\or gb\or gc\or gd\or ge\or gf\or gg\or gh\or gi\or gj%',"\n";
    echo '      \or gk\or gl\or gm\or gn\or go\or gp\or gq\or gr\or gs\or gt%',"\n";
    echo '      \or gu\or gv\or gw\or gx\or gy\or gz\or gæ\or gø\or gå%',"\n";
    echo '      \or ha\or hb\or hc\or hd\or he\or hf\or hg\or hh\or hi\or hj%',"\n";
    echo '      \or hk\or hl\or hm\or hn\or ho\or hp\or hq\or hr\or hs\or ht%',"\n";
    echo '      \or hu\or hv\or hw\or hx\or hy\or hz\or hæ\or hø\or hå%',"\n";
    echo '      \else\@ctrerr\fi',"\n";
    echo '}',"\n";
    echo '\makeatother',"\n";
}
else {
    echo '\newcommand{\pnote}[1]{}',"\n";
    echo '\newcommand{\pnotehead}[1]{}',"\n";
}

switch ($HaShem) {
  case 'HERREN':
        echo '\newcommand{\JHVH}{H{\footnotesize{ERREN}}}',"\n";
        echo '\newcommand{\JHVHs}{H{\footnotesize{ERRENS}}}',"\n";
        echo '\newcommand{\JHVHv}{H{\footnotesize{ERRE}}}',"\n";
        echo '\newcommand{\JHVHvs}{H{\footnotesize{ERRES}}}',"\n";
        break;

  case 'Herren':
        echo '\newcommand{\JHVH}{Herren}',"\n";
        echo '\newcommand{\JHVHs}{Herrens}',"\n";
        echo '\newcommand{\JHVHv}{Herre}',"\n";
        echo '\newcommand{\JHVHvs}{Herres}',"\n";
        break;

  case 'Jahve':
        echo '\newcommand{\JHVH}{Jahve}',"\n";
        echo '\newcommand{\JHVHs}{Jahves}',"\n";
        echo '\newcommand{\JHVHv}{Jahve}',"\n";
        echo '\newcommand{\JHVHvs}{Jahves}',"\n";
        break;

  case 'JHVH':
        echo '\newcommand{\JHVH}{JHVH}',"\n";
        echo '\newcommand{\JHVHs}{JHVHs}',"\n";
        echo '\newcommand{\JHVHv}{JHVH}',"\n";
        echo '\newcommand{\JHVHvs}{JHVHs}',"\n";
        break;
}
        
echo<<<'END'

\makeoddhead{ruled}{\scshape\leftmark}{}{\rightmark}
\makeoddfoot{plain}{}{}{\thepage}

\settocdepth{chapter}

%%% Allow extra space between words %%%
\sloppy

\pagestyle{ruled}
\aliaspagestyle{part}{empty}

\renewcommand{\cftchapterfont}{} % Use normal font for chapter name in TOC
\renewcommand{\cftchapterpagefont}{} % Use normal font for chapter page number in TOC

\setsubsecheadstyle{\itshape}


\title{Den Frie Bibel}
\date{2.12.2016}


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

        echo '\chapter[' . $title[$bookabb] . ']{' . $title[$bookabb] . '}\setcounter{footnote}{0}\setcounter{pagenote}{0}\renewcommand{\bibbook}{'.$abbrev[$bookabb]."}\n";
        echo $text;
        echo "\\printpagenotes*\n";
    }
}


echo "\\end{document}\n";