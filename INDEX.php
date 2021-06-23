<!DOCTYPE html>
<html>
<head>
	<title>Example</title>
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<style type="text/css">
		body {
			background-color: #f1f1f1;
		}
        .navbar {
            height : 135px;
        }
		.navbar p {
			font-size: 30px;
			font-weight: bold;
			color: white;
		}
		.collapse {
			margin-left: 500px;
		}
		.collapse li a {
			margin-left: 15px;
			font-weight: bold;
			margin-right: 30px;
		}
		.card {
         margin-top: 10px;
        -webkit-box-shadow: 0 1px 1px 0 rgba(159,167,194,.6);
        -moz-box-shadow: 0 1px 1px 0 rgba(159,167,194,.6);
         box-shadow: 0 1px 1px 0 rgba(159,167,194,.6);
         margin-left: 0%;
   		 }
   		.isi {
   			margin: 50px;
   			text-align: center;
   		 }
		.footer {
			background-color: #606060;
			height: 120px;
			text-align: center;
		}
        .nav-link{
            font-size : 25px;
        }
	</style>
</head>
<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #606060; ">
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
        <li class="nav-item">
        		<a class="nav-link" href="INDEX.php">Example</a>
      		</li>
			<li class="nav-item ">
        		<a class="nav-link" href="CHART 1.php">Chart 1</a>
      		</li>
			<li class="nav-item">
        		<a class="nav-link" href="CHART 2.php">Chart 2</a>
      		</li>
		</ul>
	</div>
	</nav>
	</header>
	<body>
		<div class="container-fluid">
   				 <div class="col-md-12">
    				  <div class="card">
                      <?php
$host = "localhost";
$user = "root";
$password = "";
$database = "whsakila";

$conn = mysqli_connect($host, $user, $password, $database);


$sql = "SELECT sum(amount) as tot from fakta_pendapatan";
$tot = mysqli_query($conn, $sql);
$tot_amount = mysqli_fetch_row($tot);

$sql = "SELECT concat('name:',f.kategori) as name, concat('y:',sum(fp.amount)*100/" . $tot_amount[0] . ") as y,
concat('drilldown:', f.kategori) as drilldown 
FROM film f 
JOIN fakta_pendapatan fp ON (f.film_id = fp.film_id) 
GROUP BY name ORDER BY y DESC";
$all_kat = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_all($all_kat)) {
    $data[] = $row;
}

$json_all_kat = json_encode($data);

//Chart ke dua
$sql = "SELECT f.kategori Kategori, SUM(fp.amount) AS tot_kat
FROM fakta_pendapatan fp 
JOIN film f ON (f.film_id = fp.film_id)
GROUP BY kategori";
$hasil_kat = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_all($hasil_kat)) {
    $tot_all_kat[] = $row;
}


function cari_tot_kat($kat_dicari, $tot_all_kat)
{
    $counter = 0;

    while ($counter < count($tot_all_kat[0])) {
        if ($kat_dicari == $tot_all_kat[0][$counter][0]) {
            $tot_kat = $tot_all_kat[0][$counter][1];
            return $tot_kat;
        }
        $counter++;
    }
}


$sql = "SELECT f.kategori kategori, t.bulan as bulan, sum(fp.amount) as pendapatan_kat 
FROM film f
JOIN fakta_pendapatan fp ON (f.film_id = fp.film_id)
JOIN time t ON (t.time_id = fp.time_id)
GROUP BY kategori, bulan";
$det_kat = mysqli_query($conn, $sql);
$i = 0;
while ($row = mysqli_fetch_all($det_kat)) {
    $data_det[] = $row;
}

$i = 0;

$string_data = "";
$string_data .= '{name:"' . $data_det[0][$i][0] . '", id:"' . $data_det[0][$i][0] . '", data: [';

foreach ($data_det[0] as $a) {

    if ($i < count($data_det[0]) - 1) {
        if ($a[0] != $data_det[0][$i + 1][0]) {
            $string_data .= '["' . $a[1] . '", ' .
                $a[2] * 100 / cari_tot_kat($a[0], $tot_all_kat) . ']]},';
            $string_data .= '{name:"' . $a[0] . '", id:"' . $a[0]    . '", data: [';
        } else {
            $string_data .= '["' . $a[1] . '", ' .
                $a[2] * 100 / cari_tot_kat($a[0], $tot_all_kat) . '], ';
        }
    } else {
        $string_data .= '["' . $a[1] . '", ' .
            $a[2] * 100 / cari_tot_kat($a[0], $tot_all_kat) . ']]}';
    }
    $i = $i + 1;
}

?>


<html>

<head>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <link rel="stylesheet" href="/drilldown.css">
</head>

<body>
    <figure class="highcharts-figure">
        <div id="container"></div>
        <center>

            <p class="highcharts-description">
            <h3> Pie chart Drilldown ketika diclick dua kali</h3>
            </p><div class="row">
            <div class="col-12">
        <div class="card-box" style="height: 500px;">
          <iframe name="mondrian" src="http://localhost:8080/mondrian/index.html" style="height:100%; width:100%; border:none; align-content:center"> </iframe>
        </div>
        </center>
    </figure>



    <script type="text/javascript">
        // Create the chart
        Highcharts.chart('container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Persentasi penyewaan film semua kategori'
            },
            subtitle: {
                text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
            },

            accessibility: {
                announceNewData: {
                    enabled: true
                },
                point: {
                    valueSuffix: '%'
                }
            },

            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y:.1f}%'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
            },

            series: [{
                name: "Pendapatan By Kategori",
                colorByPoint: true,
                data: <?php
                        $datanya = $json_all_kat;
                        $data1 = str_replace('["', '{"', $datanya);
                        $data2 = str_replace('"]', '"}', $data1);
                        $data3 = str_replace('[[', '[', $data2);
                        $data4 = str_replace(']]', ']', $data3);
                        $data5 = str_replace(':', '" : "', $data4);
                        $data6 = str_replace('"name"', 'name', $data5);
                        $data7 = str_replace('"drilldown"', 'drilldown', $data6);
                        $data8 = str_replace('"y"', 'y', $data7);
                        $data9 = str_replace('",', ',', $data8);
                        $data10 = str_replace(',y', '",y', $data9);
                        $data11 = str_replace(',y : "', ',y : ', $data10);
                        echo $data11;
                        ?>
            }],
            drilldown: {
                series: [
                    <?php
                    //Teknik Clean
                    echo $string_data;
                    ?>

                ]
            }
        });
    </script>
</body>

</html>				  
    				  </div>
  				</div>
		</div>
	</body>
<br>
<div class="footer">
	
</div>
</body>
</html>