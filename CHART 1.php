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
			height: 200px;
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

$dbHost = "localhost";
$dbDatabase = "whsakila";
$dbUser = "root";
$dbPassword = "";

$conn = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDatabase);

$sql1 = mysqli_query($conn, "SELECT count(customer_id) as total FROM fakta_pendapatan");
$total = mysqli_fetch_row($sql1);

$sql = "SELECT concat('name:', s.nama_kota) as name, concat('y:',count(fp.customer_id)) as y, concat('drilldown:', s.nama_kota) as drilldown
FROM fakta_pendapatan fp JOIN store s ON fp.store_id=s.store_id
GROUP BY s.nama_kota";

$toko = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_all($toko)) {
  $data[] = $row;
}
$json_toko = json_encode($data);
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
            </p>
        </center>
    </figure>

    <script type="text/javascript">
  // Create the chart
  Highcharts.chart('container', {
    chart: {
      type: 'column'
    },
    title: {
      text: 'Persentase Jumlah Pembeli Tiap Toko (WH Sakila)'
    },
    subtitle: {
      text: 'Sorot di potongan kue untuk melihat detail nilai '
    },

    accessibility: {
      announceNewData: {
        enabled: true
      },
      point: {
        valueSuffix: ''
      }
    },

    plotOptions: {
      series: {
        dataLabels: {
          enabled: true,
          format: '{point.name}: {point.y:.1f}'
        }
      }
    },

    tooltip: {
      headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
      pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total</br>'
    },

    series: [{
      name: "Customer by Store",
      colorByPoint: true,
      data: <?php
            //TEKNIK GA JELAS

            $datanya = $json_toko;
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
        //TEKNIK CLEAN
        //echo $string_data;
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