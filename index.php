<?php
    //
    // vnStat PHP frontend (c)2006-2010 Bjorge Dijkstra (bjd@jooz.net)
    //
    // This program is free software; you can redistribute it and/or modify
    // it under the terms of the GNU General Public License as published by
    // the Free Software Foundation; either version 2 of the License, or
    // (at your option) any later version.
    //
    // This program is distributed in the hope that it will be useful,
    // but WITHOUT ANY WARRANTY; without even the implied warranty of
    // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    // GNU General Public License for more details.
    //
    // You should have received a copy of the GNU General Public License
    // along with this program; if not, write to the Free Software
    // Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    //
    //
    // see file COPYING or at http://www.gnu.org/licenses/gpl.html
    // for more information.
    //
    require 'config.php';
    require 'localize.php';
    require 'vnstat.php';

    validate_input();

    require "./themes/$style/theme.php";

    function write_side_bar()
    {
        global $iface, $page, $graph, $script, $style;
        global $iface_list, $iface_title;
        global $page_list, $page_title;

        $p = "&amp;graph=$graph&amp;style=$style";

        print '<ul class="iface nav">';
        foreach ($iface_list as $if)
        {
            if ($iface == $if) {
                print '<li class="iface active">';
            } else {
                print '<li class=iface>';
            }
            print "<a href=\"$script?if=$if$p\">";
            if ( isset($iface_title[$if]) ) {
                print $iface_title[$if];
            } else {
                print $if;
            }
            print '</a> <ul class="page">';
            foreach ($page_list as $pg) {
                print "<li class=\"page\"><a href=\"$script?if=$if$p&amp;page=$pg\">".$page_title[$pg]."</a>\n";
            }
            print "</ul>\n";
        }
        print "</ul>\n";
    }


    function kbytes_to_string($kb)
    {
        $units = array('TB','GB','MB','KB');
        $scale = 1024*1024*1024;
        $ui = 0;

        while (($kb < $scale) && ($scale > 1))
        {
            $ui++;
            $scale = $scale / 1024;
        }
        return sprintf("%0.2f %s", ($kb/$scale),$units[$ui]);
    }

    function write_summary()
    {
        global $summary,$top,$day,$hour,$month;

        $trx = $summary['totalrx']*1024+$summary['totalrxk'];
        $ttx = $summary['totaltx']*1024+$summary['totaltxk'];

        //
        // build array for write_data_table
        //
        $sum[0]['act'] = 1;
        $sum[0]['label'] = T('This hour');
        $sum[0]['rx'] = $hour[0]['rx'];
        $sum[0]['tx'] = $hour[0]['tx'];

        $sum[1]['act'] = 1;
        $sum[1]['label'] = T('This day');
        $sum[1]['rx'] = $day[0]['rx'];
        $sum[1]['tx'] = $day[0]['tx'];

        $sum[2]['act'] = 1;
        $sum[2]['label'] = T('This month');
        $sum[2]['rx'] = $month[0]['rx'];
        $sum[2]['tx'] = $month[0]['tx'];

        $sum[3]['act'] = 1;
        $sum[3]['label'] = T('All time');
        $sum[3]['rx'] = $trx;
        $sum[3]['tx'] = $ttx;

        write_data_table(T('Summary'), $sum);
        print "<br/>\n";
        write_data_table(T('Top 10 days'), $top);
    }


    function write_data_table($caption, $tab)
    {
        print "<table class=data-table >\n";
        print "<caption>$caption</caption>\n";
        print "<thead><tr>";
        print "<th class=span-15>&nbsp;</th>";
        print "<th class=span-25>".T('In')."</th>";
        print "<th class=span-25>".T('Out')."</th>";
        print "<th class=span-25>".T('Total')."</th>";
        print "</tr></thead>\n";

        for ($i=0; $i<count($tab); $i++)
        {
            if ($tab[$i]['act'] == 1)
            {
                $t = $tab[$i]['label'];
                $rx = kbytes_to_string($tab[$i]['rx']);
                $tx = kbytes_to_string($tab[$i]['tx']);
                $total = kbytes_to_string($tab[$i]['rx']+$tab[$i]['tx']);
                print "<tr>";
                print "<td>$t</td>";
                print "<td>$rx</td>";
                print "<td>$tx</td>";
                print "<td>$total</td>";
                print "</tr>\n";
             }
        }
        print "</table>\n";
    }

    get_vnstat_data();

    //
    // html start
    //
    header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>vnStat - PHP frontend</title>
  <meta name="viewport" content="width=device-width">

  
  <link rel="stylesheet" href="themes/<?php echo $style ?>/style.css"/>

</head>
<body class=<?php echo $style ?>>

<div class="wrap cf" id=wrap>
  <nav class=navigation id=sidebar>
    <a class="nav-toggle">Toggle</a>
    <?php write_side_bar(); ?>
  </nav>
  <div class=content id=content>
    <div class=header id=header>
      <h1><?php print T('Traffic data for')." $iface_title[$iface] ($iface)";?></h1>
    </div>
    <div class=main id=main>

    <?php
    $graph_params = "if=$iface&amp;page=$page&amp;style=$style&amp;graph=$graph";
    if ($page != 's') {
      echo '<div class=graph>';
        if ($graph_format == 'svg') {
       print "<object type=\"image/svg+xml\" data=\"graph_svg.php?$graph_params\"></object>\n";
        } else {
       print "<img src=\"graph.php?$graph_params\" alt=\"graph\"/>\n";
        }
      echo '</div> <!-- .graph -->';
     }

    if ($page == 's')
    {
        write_summary();
    }
    else if ($page == 'h')
    {
        write_data_table(T('Last 24 hours'), $hour);
    }
    else if ($page == 'd')
    {
        write_data_table(T('Last 30 days'), $day);
    }
    else if ($page == 'm')
    {
        write_data_table(T('Last 12 months'), $month);
    }
    ?>
    </div>
    <div id="footer"><a href="http://www.sqweek.com/">vnStat PHP frontend</a> 1.5.2 - &copy;2006-2011 Bjorge Dijkstra (bjd _at_ jooz.net)</div>
  </div>
</div> <!-- .wrap -->

<?php
    if (is_file('themes/'.$style.'/js/script.js') ) {
      echo '<script src="themes/'.$style.'/js/script.js"></script>';
    }
?>

</body>
</html>
