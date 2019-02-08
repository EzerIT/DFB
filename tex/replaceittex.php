<?php

require_once('../oversigt.inc.php');

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

    $from[] = '/^ *\*\*\* *$/m';
    $to[] = '\\vspace{\\baselineskip}';

    $from[] = '/\*([^\*]+)\*/';
    $to[] = '\\emph{\1}';

    $from[] = '/\s*{K:\s*([^}]+)}/';
    $to[] = '';

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

    $from[] = '/Sl (\d)/';
    $to[] = 'Sl~\1';
    
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

if ($_SERVER['argc']!=7
    || !in_array($_SERVER['argv'][1], array('Jahve', 'JHVH', 'HERREN', 'Herren'))
    || !in_array($_SERVER['argv'][2], array('r', 'd', 'f'))
    || !in_array($_SERVER['argv'][3], array('+v', '-v'))
    || !in_array($_SERVER['argv'][4], array('+f', '-f'))
    || !in_array($_SERVER['argv'][5], array('+s', '-s'))
    || !in_array($_SERVER['argv'][6], array('+o', '-o'))
    ) {
    fwrite(STDERR,"brug: php replaceittex.php <HaShem> <modenhed> [+|-]v [+|-]f [+|-]s [+|-]o\n"
                . "      hvor <HaShem> er 'Jahve', 'JHVH', 'HERREN' eller 'Herren'\n"
                . "      <modenhed> er 'r', 'd' eller 'f' (rå oversættelse, delvis færdig, færdig)\n"
                . "      +/-v står for med/uden versnumre\n"
                . "      +/-f står for med/uden fodnoter\n"
                . "      +/-s står for med/uden slutnoter\n"
                . "      +/-o står for med/uden overskrifter\n");
    exit(1);
}

$HaShem = $_SERVER['argv'][1];

switch ($_SERVER['argv'][2]) {
  case 'r':
        $include_status[] = 'btn-warning';
        // Fall through

  case 'd':
        $include_status[] = 'btn-info';
        // Fall through

  case 'f':
        $include_status[] = 'btn-success';
}
        
$with_verses = $_SERVER['argv'][3] == '+v';
$with_footnotes = $_SERVER['argv'][4] == '+f';
$with_endnotes = $_SERVER['argv'][5] == '+s';
$with_headings = $_SERVER['argv'][6] == '+o';

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
    echo '      \or ia\or ib\or ic\or id\or ie\or if\or ig\or ih\or ii\or ij%',"\n";
    echo '      \or ik\or il\or im\or in\or io\or ip\or iq\or ir\or is\or it%',"\n";
    echo '      \or iu\or iv\or iw\or ix\or iy\or iz\or iæ\or iø\or iå%',"\n";
    echo '      \or ja\or jb\or jc\or jd\or je\or jf\or jg\or jh\or ji\or jj%',"\n";
    echo '      \or jk\or jl\or jm\or jn\or jo\or jp\or jq\or jr\or js\or jt%',"\n";
    echo '      \or ju\or jv\or jw\or jx\or jy\or jz\or jæ\or jø\or jå%',"\n";
    echo '      \or ka\or kb\or kc\or kd\or ke\or kf\or kg\or kh\or ki\or kj%',"\n";
    echo '      \or kk\or kl\or km\or kn\or ko\or kp\or kq\or kr\or ks\or kt%',"\n";
    echo '      \or ku\or kv\or kw\or kx\or ky\or kz\or kæ\or kø\or kå%',"\n";
    echo '      \or la\or lb\or lc\or ld\or le\or lf\or lg\or lh\or li\or lj%',"\n";
    echo '      \or lk\or ll\or lm\or ln\or lo\or lp\or lq\or lr\or ls\or lt%',"\n";
    echo '      \or lu\or lv\or lw\or lx\or ly\or lz\or læ\or lø\or lå%',"\n";
    echo '      \or ma\or mb\or mc\or md\or me\or mf\or mg\or mh\or mi\or mj%',"\n";
    echo '      \or mk\or ml\or mm\or mn\or mo\or mp\or mq\or mr\or ms\or mt%',"\n";
    echo '      \or mu\or mv\or mw\or mx\or my\or mz\or mæ\or mø\or må%',"\n";
    echo '      \or na\or nb\or nc\or nd\or ne\or nf\or ng\or nh\or ni\or nj%',"\n";
    echo '      \or nk\or nl\or nm\or nn\or no\or np\or nq\or nr\or ns\or nt%',"\n";
    echo '      \or nu\or nv\or nw\or nx\or ny\or nz\or næ\or nø\or nå%',"\n";
    echo '      \or oa\or ob\or oc\or od\or oe\or of\or og\or oh\or oi\or oj%',"\n";
    echo '      \or ok\or ol\or om\or on\or oo\or op\or oq\or or\or os\or ot%',"\n";
    echo '      \or ou\or ov\or ow\or ox\or oy\or oz\or oæ\or oø\or oå%',"\n";
    echo '      \or pa\or pb\or pc\or pd\or pe\or pf\or pg\or ph\or pi\or pj%',"\n";
    echo '      \or pk\or pl\or pm\or pn\or po\or pp\or pq\or pr\or ps\or pt%',"\n";
    echo '      \or pu\or pv\or pw\or px\or py\or pz\or pæ\or pø\or på%',"\n";
    echo '      \or qa\or qb\or qc\or qd\or qe\or qf\or qg\or qh\or qi\or qj%',"\n";
    echo '      \or qk\or ql\or qm\or qn\or qo\or qp\or qq\or qr\or qs\or qt%',"\n";
    echo '      \or qu\or qv\or qw\or qx\or qy\or qz\or qæ\or qø\or qå%',"\n";
    echo '      \or ra\or rb\or rc\or rd\or re\or rf\or rg\or rh\or ri\or rj%',"\n";
    echo '      \or rk\or rl\or rm\or rn\or ro\or rp\or rq\or rr\or rs\or rt%',"\n";
    echo '      \or ru\or rv\or rw\or rx\or ry\or rz\or ræ\or rø\or rå%',"\n";
    echo '      \or sa\or sb\or sc\or sd\or se\or sf\or sg\or sh\or si\or sj%',"\n";
    echo '      \or sk\or sl\or sm\or sn\or so\or sp\or sq\or sr\or ss\or st%',"\n";
    echo '      \or su\or sv\or sw\or sx\or sy\or sz\or sæ\or sø\or så%',"\n";
    echo '      \or ta\or tb\or tc\or td\or te\or tf\or tg\or th\or ti\or tj%',"\n";
    echo '      \or tk\or tl\or tm\or tn\or to\or tp\or tq\or tr\or ts\or tt%',"\n";
    echo '      \or tu\or tv\or tw\or tx\or ty\or tz\or tæ\or tø\or tå%',"\n";
    echo '      \or ua\or ub\or uc\or ud\or ue\or uf\or ug\or uh\or ui\or uj%',"\n";
    echo '      \or uk\or ul\or um\or un\or uo\or up\or uq\or ur\or us\or ut%',"\n";
    echo '      \or uu\or uv\or uw\or ux\or uy\or uz\or uæ\or uø\or uå%',"\n";
    echo '      \or va\or vb\or vc\or vd\or ve\or vf\or vg\or vh\or vi\or vj%',"\n";
    echo '      \or vk\or vl\or vm\or vn\or vo\or vp\or vq\or vr\or vs\or vt%',"\n";
    echo '      \or vu\or vv\or vw\or vx\or vy\or vz\or væ\or vø\or vå%',"\n";
    echo '      \or wa\or wb\or wc\or wd\or we\or wf\or wg\or wh\or wi\or wj%',"\n";
    echo '      \or wk\or wl\or wm\or wn\or wo\or wp\or wq\or wr\or ws\or wt%',"\n";
    echo '      \or wu\or wv\or ww\or wx\or wy\or wz\or wæ\or wø\or wå%',"\n";
    echo '      \or xa\or xb\or xc\or xd\or xe\or xf\or xg\or xh\or xi\or xj%',"\n";
    echo '      \or xk\or xl\or xm\or xn\or xo\or xp\or xq\or xr\or xs\or xt%',"\n";
    echo '      \or xu\or xv\or xw\or xx\or xy\or xz\or xæ\or xø\or xå%',"\n";
    echo '      \or ya\or yb\or yc\or yd\or ye\or yf\or yg\or yh\or yi\or yj%',"\n";
    echo '      \or yk\or yl\or ym\or yn\or yo\or yp\or yq\or yr\or ys\or yt%',"\n";
    echo '      \or yu\or yv\or yw\or yx\or yy\or yz\or yæ\or yø\or yå%',"\n";
    echo '      \or za\or zb\or zc\or zd\or ze\or zf\or zg\or zh\or zi\or zj%',"\n";
    echo '      \or zk\or zl\or zm\or zn\or zo\or zp\or zq\or zr\or zs\or zt%',"\n";
    echo '      \or zu\or zv\or zw\or zx\or zy\or zz\or zæ\or zø\or zå%',"\n";
    echo '      \else\@ctrerr\fi',"\n";
    echo '}',"\n";
    echo '\makeatother',"\n";
}
else {
    echo '\newcommand{\pnote}[1]{}',"\n";
    echo '\newcommand{\pnotehead}[1]{}',"\n";
}

if (!$with_headings)
    echo '\renewcommand{\subsection}[2][]{}',"\n";


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
\date{13.02.2018}


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