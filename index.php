<html>
<head>
	<title>Information Security Project</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	<script src="js/jquery-2.1.1.min.js"></script>
    <link href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script type="text/javascript">
    function check_file()
    {
        str=document.getElementById('file').value.toUpperCase();
        suffix=".PCAP";
        if(str.indexOf(suffix, str.length - suffix.length) == -1)
        {
        	alert("File type not allowed,\nAllowed file: *.pcap");
            document.getElementById('file').value = '';
        }
    }
    $(document).ready(function(){
    	$("#accordion").accordion({"collapsible": true});
    });
    </script>
</head>
<body>
	<div class="container">
  		<div class="row">
			<div class="col-lg-12">
		      <h2><i class="glyphicon glyphicon-briefcase"></i> Packet analyzer</h2>
				<hr>
				<div class="alert alert-info">
        			<button type="button" class="close" data-dismiss="alert">×</button>
        			Please remember that we never store your data, so please save your information after processing a file.
      			</div>
      		</div>
      	</div>
      	<div id="accordion">
      		<h3>Please upload your file</h3>
			<div class="row">
				<div class="col-lg-12">
				      		<form method="POST" enctype="multipart/form-data" action="">
				      		<label for="file">Upload your file: </label><input type="file" name="archivo" onchange="check_file()" id="file" />
				      		<br>
				      			<label for="ip">Enter an IP (optional):</label>
				      			<input type="text" name="ip" id="ip" maxlength="15" />
								<input type="radio" name="ip_src" value="src">Source
								<input type="radio" name="ip_src" value="dst">Destination
								<input type="radio" name="ip_src" value="both" checked="true">Both
				      			<br>
				      			<br>
					    		<label for="filter"> Please select the protocol that you want to filter: </label>
					    		<select id="filter" name="filter">
					    			<option value="all">All information</option>
					    			<option value="tcp">TCP</option>
					    			<option value="udp">UDP</option>
					    			<option value="icmp">ICMP</option>
					    			<option value="http">HTTP</option>
					    			<option value="https">HTTPS</option>
					    			<option value="stcṕ">TCP statistics</option>
					    			<option value="sudp">UDP statistics</option>
					    			<option value="sip">IP statistics</option>
					    			<option value="iplist">IP list</option>
					    			<option value="tcpudp">Amount of TCP and UDP</option>
					    		</select>
					    		<hr>
					    		<center><input type="submit" value="Process" name="enviar" accept=".pcap"/></center>
							</form>
			    	</div>
				</div>
				<h3>Output</h3>
				<div class="row" id="output" style="overflow-y: scroll;">
			    	<div class="col-lg-12">
			    			<p id="salida"></p>
			    	</div>
				</div>
		</div>
    </div>
</body>
</html>
<?php 
	if(isset($_POST['enviar'])) {
		$filter = $_POST["filter"];
		$ip = htmlspecialchars($_POST["ip"], ENT_QUOTES, 'UTF-8');
		$ipSrc = $_POST["ip_src"];
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES['archivo']['name']);
		$uploadOk = 1;
		if($_FILES["archivo"]["name"] == ""){
			echo"<script>alert(\"I can't work with an empty file! ☹\");</script>";
		}else{
			// Check if file already exists
			if (file_exists($target_file)) {
			    echo "Sorry, file already exists.\n";
			    $uploadOk = 0;
			}else{
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				    echo "Sorry, your file was not uploaded.\n";
				// if everything is ok, try to upload file
				} else {
				    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $target_file)) {
				    	if ($ip != "") {
				    		if ($ipSrc == "both") {
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -R \"ip.addr == "	. $ip . "\"");
					        	$contenido = str_replace( "\n", '<br><hr>', $contenido );
					    		echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    		}elseif ($ipSrc == "src") {
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -R \"ip.src == " 	. $ip . "\"");
					        	$contenido = str_replace( "\n", '<br><hr>', $contenido );
					    		echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    		}elseif ($ipSrc == "dst") {
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -R \"ip.dst == " 	. $ip . "\"");
					        	$contenido = str_replace( "\n", '<br><hr>', $contenido );
					    		echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    		}
				    		
				    	}else{
				    		switch ($filter) {
				    		case 'tcp':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -O TCP");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'udp':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -O UDP");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'tcpudp':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -q -z ptype,tree");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'iplist':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -o column.format:'\"Source\", \"%s\",\"Destination\", \"%d\"' -Ttext");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'stcṕ':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -q -z conv,tcp");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'sudp':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -q -z conv,udp");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'sip':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -q -z ip_hosts,tree");
				        		$contenido = str_replace( "\n", '<br>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'all':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file);
				        		$contenido = str_replace( "\n", '<br><hr>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
								break;
							case 'http':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -Y tcp.port==80");
				        		$contenido = str_replace( "\n", '<br><hr>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;
				    		case 'https':
				    			$contenido = shell_exec("./bin/tshark -r " . $target_file . " -Y tcp.port==443");
				        		$contenido = str_replace( "\n", '<br><hr>', $contenido );
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('$contenido');</script>";
				    			break;			    			
				    		default:
				    			echo "<script>$(\"#accordion\").accordion({\"active\": 1}); $(\"#salida\").html('ERROR!');</script>";
				    			break;
				    	}
				    	}
				        exec("rm " . $target_file);
				    } else {
				        echo "Sorry, there was an error uploading your file.\n";
				    }
				}
			}
		}
	}
?>