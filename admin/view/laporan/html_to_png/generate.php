<?php   
	if (isset($_POST['imgBase64'])) {
		# code...
		// Requires php5   
		define('UPLOAD_DIR', 'images/');   
		$img = $_POST['imgBase64'];   
		$img = str_replace('data:image/png;base64,', '', $img);   
		$img = str_replace(' ', '+', $img);   
		$data = base64_decode($img);   
		$file = UPLOAD_DIR . uniqid() . '.png';   
		$success = file_put_contents($file, $data);   
		print $success ? $file : 'Unable to save the file.';   
		exit();
	}
?> 
<!DOCTYPE html> 
<html> 

<head> 
	<title></title> 
	<link rel="stylesheet" href= 
"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
	<script src= 
"https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"> 
	</script> 
	<script src= 
"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"> 
	</script> 
	<script src= 
"https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"> 
	</script> 
</head> 

<body> 

	<div style="width: 80mm"> 
		<div id="createImg">
			<center><img src="logohehaocean.png" width="50%"></center>
			<center>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</center>
			<table width="100%">
				<tbody>
					<tr>
						<td width="50%"><img src="qr.png" width="100%"></td>
						<td style="padding-right: : 30px;">
							<table width="100%" style="font-size: 80%;">
								<tbody>
									<tr>
										<td>Tiket</td>
										<td> : </td>
										<td>Jumat Siang</td>
									</tr>
									<tr>
										<td colspan="3">----------------------------------------</td>
									</tr>
									<tr>
										<td>Tanggal</td>
										<td> : </td>
										<td> 17 November 2020</td>
									</tr>
									<tr>
										<td colspan="3">----------------------------------------</td>
									</tr>
									<tr>
										<td>Jam</td>
										<td> : </td>
										<td> 13:11:02 WIB</td>
									</tr>
									<tr>
										<td colspan="3">----------------------------------------</td>
									</tr>
									<tr>
										<td>Kode</td>
										<td> : </td>
										<td>X20435112</td>
									</tr>
									<tr>
										<td colspan="3">----------------------------------------</td>
									</tr>
									<tr>
										<td>Admin</td>
										<td> : </td>
										<td>AdminTiket</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<center>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</center>
		</div> 
		<button id="geeks" type="button"
									class="btn btn-primary top"> 
			Create Image</button> 
		<div id="img" style="display:none;"> 
			<img src="" id="newimg" class="top" /> 
		</div> 
	</div> 
	<script> 
		$(function() { 
			$("#geeks").click(function() { 
				html2canvas($("#createImg"), { 
					onrendered: function(canvas) { 
						var imgsrc = canvas.toDataURL("image/png"); 
						console.log(imgsrc); 
						$("#newimg").attr('src', imgsrc); 
						$("#img").show(); 
						var dataURL = canvas.toDataURL(); 
						$.ajax({ 
							type: "POST", 
							data: { 
								imgBase64: dataURL 
							} 
						}).done(function(o) { 
							console.log('saved'); 
						}); 
					} 
				}); 


			}); 
		}); 
	</script> 
</body> 

</html> 
