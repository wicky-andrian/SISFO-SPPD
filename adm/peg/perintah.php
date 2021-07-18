<?php
//////////////////////////////////////////////////////////////////////
// SISFO-SPPD, Sistem Informasi Surat Perintah Perjalanan Dinas.    //
// Cocok untuk kantor - kantor yang membutuhkan pengarsipan surat   //
// perintah tugas perjalanan dinas.                                 //
//////////////////////////////////////////////////////////////////////
// Dikembangkan oleh : Agus Muhajir                                 //
// E-Mail : hajirodeon@gmail.com                                    //
// HP/SMS/WA : 081-829-88-54                                        //
// source code : http://github.com/hajirodeon                       //
//////////////////////////////////////////////////////////////////////



session_start();

require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/adm.php");
require("../../inc/class/paging.php");
$tpl = LoadTpl("../../template/admin.html");

nocache;

//nilai
$filenya = "perintah.php";
$judul = "[PEGAWAI/PEJABAT]. Pemberi Perintah";
$judulku = "$judul";
$judulx = $judul;
$s = nosql($_REQUEST['s']);


$limit = 10;





//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//nek batal
if ($_POST['btnBTL'])
	{
	//re-direct
	xloc($filenya);
	exit();
	}



//jika edit
if ($s == "edit")
	{
	$kdx = nosql($_REQUEST['kd']);

	$qx = mysqli_query($koneksi, "SELECT * FROM m_pemberi_perintah ".
						"WHERE kd = '$kdx'");
	$rowx = mysqli_fetch_assoc($qx);
	$e_peg_kd = balikin($rowx['peg_kd']);
	$e_peg_nip = balikin($rowx['peg_nip']);
	$e_peg_nama = balikin($rowx['peg_nama']);
	$e_peg_namax = "$e_peg_nama NIP.$e_peg_nip";
	$e_jabatan_kd = balikin($rowx['jabatan_kd']);
	$e_jabatan_nama = balikin($rowx['jabatan_nama']);
	$e_valid_awal = balikin($rowx['valid_awal']);
	$e_valid_akhir = balikin($rowx['valid_akhir']);
	
	
	//pecah tanggal
	$tgl2_pecah = balikin($e_valid_awal);
	$tgl2_pecahku = explode("-", $tgl2_pecah);
	$tgl2_tgl = trim($tgl2_pecahku[2]);
	$tgl2_bln = trim($tgl2_pecahku[1]);
	$tgl2_thn = trim($tgl2_pecahku[0]);
	$e_valid_awal = "$tgl2_thn-$tgl2_bln-$tgl2_tgl";
		
	
	
	//pecah tanggal
	$tgl2_pecah = balikin($e_valid_akhir);
	$tgl2_pecahku = explode("-", $tgl2_pecah);
	$tgl2_tgl = trim($tgl2_pecahku[2]);
	$tgl2_bln = trim($tgl2_pecahku[1]);
	$tgl2_thn = trim($tgl2_pecahku[0]);
	$e_valid_akhir = "$tgl2_thn-$tgl2_bln-$tgl2_tgl";
		
	
	
	
	$e_ket = balikin($rowx['ket']);
	}



//jika simpan
if ($_POST['btnSMP'])
	{
	$s = nosql($_POST['s']);
	$kd = nosql($_POST['kd']);
	$page = nosql($_POST['page']);
	$e_kunci = cegah($_POST['kunci']);
	//$e_peg_kd = balikin($_POST['e_peg_kd']);
	$e_jabatan_kd = balikin($_POST['e_jabatan_kd']);
	
	
	//pecah nip
	$nipku = explode("NIP.", $e_kunci);
	$e_peg_nip = trim($nipku[1]);
	
	
	
		
	//detail 
	$qx = mysqli_query($koneksi, "SELECT * FROM m_pegawai ".
						"WHERE nip = '$e_peg_nip'");
	$rowx = mysqli_fetch_assoc($qx);
	$e_peg_kd = cegah($rowx['kd']);
	$e_peg_nama = cegah($rowx['nama']);


	//jika ada, lanjutkan
	if (!empty($e_peg_kd))
		{
		//jabatan
		$qx = mysqli_query($koneksi, "SELECT * FROM m_jabatan_perintah ".
										"WHERE kd = '$e_jabatan_kd'");
		$rowx = mysqli_fetch_assoc($qx);
		$e_jabatan_nama = cegah($rowx['nama']);
		
		
		
		$e_valid_awalx = balikin($_POST['e_tgl1']);
		
		//pecah tanggal
		$tgl2_pecah = balikin($e_valid_awalx);
		$tgl2_pecahku = explode("-", $tgl2_pecah);
		$tgl2_tgl = trim($tgl2_pecahku[2]);
		$tgl2_bln = trim($tgl2_pecahku[1]);
		$tgl2_thn = trim($tgl2_pecahku[0]);
		$e_valid_awal = "$tgl2_thn:$tgl2_bln:$tgl2_tgl";
			
		
		
		$e_valid_akhirx = balikin($_POST['e_tgl2']);
		
		//pecah tanggal
		$tgl2_pecah = balikin($e_valid_akhirx);
		$tgl2_pecahku = explode("-", $tgl2_pecah);
		$tgl2_tgl = trim($tgl2_pecahku[2]);
		$tgl2_bln = trim($tgl2_pecahku[1]);
		$tgl2_thn = trim($tgl2_pecahku[0]);
		$e_valid_akhir = "$tgl2_thn:$tgl2_bln:$tgl2_tgl";
			
			
			
			
		$e_ket = balikin($_POST['e_ket']);
	
		$ke = $filenya;
	
	
	
		//jika baru
		if (empty($s))
			{
			//cek
			$qcc = mysqli_query($koneksi, "SELECT * FROM m_pemberi_perintah ".
									"WHERE peg_kd = '$e_peg_kd'");
			$rcc = mysqli_fetch_assoc($qcc);
			$tcc = mysqli_num_rows($qcc);
				
			//nek ada
			if (!empty($tcc))
				{
				//re-direct
				$pesan = "Sudah Ada. Silahkan Ganti Yang Lain...!!";
				pekem($pesan,$ke);
				exit();
				}
			else
				{
				mysqli_query($koneksi, "INSERT INTO m_pemberi_perintah(kd, peg_kd, peg_nip, peg_nama, ".
								"jabatan_kd, jabatan_nama, valid_awal, ".
								"valid_akhir, ket, postdate) VALUES ".
								"('$x', '$e_peg_kd', '$e_peg_nip', '$e_peg_nama', ".
								"'$e_jabatan_kd', '$e_jabatan_nama', '$e_valid_awal', ".
								"'$e_valid_akhir', '$e_ket', '$today')");

				//masukin ke database
				$kode = "$e_peg_nip";
				mysqli_query($koneksi, "INSERT INTO user_history(kd, user_kd, user_nip, ".
							"user_nama, user_jabatan, perintah_sql, ".
							"menu_ket, postdate) VALUES ".
							"('$x', 'admin', 'admin', ".
							"'admin', 'admin', 'ENTRI BARU : $kode', ".
							"'$judul', '$today')");

						
							
				//re-direct
				xloc($ke);
				exit();
				}
			}
	
		//jika update
		if ($s == "edit")
			{
			mysqli_query($koneksi, "UPDATE m_pemberi_perintah SET peg_kd = '$e_peg_kd', ".
							"peg_nip = '$e_peg_nip', ".
							"peg_nama = '$e_peg_nama', ".
							"jabatan_kd = '$e_jabatan_kd', ".
							"jabatan_nama = '$e_jabatan_nama', ".
							"valid_awal = '$e_valid_awal', ".
							"valid_akhir = '$e_valid_akhir', ".
							"ket = '$e_ket', ".
							"postdate = '$today' ".
							"WHERE kd = '$kd'");
	
			//masukin ke database
			$kode = "$e_peg_nip";
			mysqli_query($koneksi, "INSERT INTO user_history(kd, user_kd, user_nip, ".
						"user_nama, user_jabatan, perintah_sql, ".
						"menu_ket, postdate) VALUES ".
						"('$x', 'admin', 'admin', ".
						"'admin', 'admin', 'UPDATE : $kode', ".
						"'$judul', '$today')");
	
	
	
			//re-direct
			xloc($ke);
			exit();
			}
		}
		
	else
		{
		//re-direct
		xloc($ke);
		exit();	
		}
	}





//jika hapus
if ($_POST['btnHPS'])
	{
	//ambil nilai
	$jml = nosql($_POST['jml']);
	$page = nosql($_POST['page']);
	$ke = $filenya;

	//ambil semua
	for ($i=1; $i<=$jml;$i++)
		{
		//ambil nilai
		$yuk = "item";
		$yuhu = "$yuk$i";
		$kd = nosql($_POST["$yuhu"]);

		//del
		mysqli_query($koneksi, "DELETE FROM m_pemberi_perintah ".
						"WHERE kd = '$kd'");
		}


	//auto-kembali
	xloc($ke);
	exit();
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



//isi *START
ob_start();




?>

	
<script>
$(document).ready(function() {
  		
	$.noConflict();
    
});
</script>
  




<?php




//require
require("../../inc/js/jumpmenu.js");
require("../../inc/js/checkall.js");
require("../../inc/js/swap.js");


//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<div class="row">
	<div class="col-md-3">
	<form action="'.$filenya.'" method="post" name="formx2">

		<p>
		PEGAWAI : 
		<br>

		<input type="text" name="kunci" id="kunci" class="btn btn-warning" placeholder="Nama Pegawai" value="'.$e_peg_namax.'" required>
		<input type="hidden" name="e_peg_kd" id="e_peg_kd" value="'.$e_peg_kd.'">
		</p>


		<br>


		<p>
		JABATAN SEBAGAI : 
		<br>
		<select name="e_jabatan_kd" class="btn btn-warning" required>
		<option value="'.$e_jabatan_kd.'" selected>'.$e_jabatan_nama.'</option>';
		
		//list
		$qku = mysqli_query($koneksi, "SELECT * FROM m_jabatan_perintah ".
										"ORDER BY nama ASC");
		$rku = mysqli_fetch_assoc($qku);
		
		do
			{
			//nilai
			$ku_kd = balikin($rku['kd']);
			$ku_nama = balikin($rku['nama']);
		
			echo '<option value="'.$ku_kd.'">'.$ku_nama.'</option>';
			}
		while ($rku = mysqli_fetch_assoc($qku));
		
		
		echo '</select>
		</p>
		<br>


		<p>
		VALID AWAL : 
		<br>
		<input name="e_tgl1" id="e_tgl1" type="date" value="'.$e_valid_awal.'" class="btn btn-warning">
		</p>
		<br>


		<p>
		VALID AKHIR : 
		<br>
		<input name="e_tgl2" id="e_tgl2" type="date" value="'.$e_valid_akhir.'" class="btn btn-warning">
		</p>
		

		<p>
		KET : 
		<br>
		<input name="e_ket" type="text" size="20" value="'.$e_ket.'" class="btn btn-warning">
		</p>
		<br>
		
				
		<p>
		<input name="s" type="hidden" value="'.$s.'">
		<input name="kd" type="hidden" value="'.$kdx.'">
		<input name="page" type="hidden" value="'.$page.'">
		
		<input name="btnSMP" type="submit" value="SIMPAN" class="btn btn-danger">
		<input name="btnBTL" type="submit" value="BATAL" class="btn btn-info">
		</p>
		
		</form>
		
	</div>
	

	<div class="col-md-9">';


	echo '<form action="'.$filenya.'" method="post" name="formx">';


		//query
		$p = new Pager();
		$start = $p->findStart($limit);
		
		$sqlcount = "SELECT * FROM m_pemberi_perintah ".
						"ORDER BY peg_nama ASC, ".
						"jabatan_nama ASC";
		$sqlresult = $sqlcount;
		
		$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
		$pages = $p->findPages($count, $limit);
		$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
		$target = "$filenya?kunci=$kunci";
		$pagelist = $p->pageList($_GET['page'], $pages, $target);
		$data = mysqli_fetch_array($result);


		echo '<div class="table-responsive">          
		<table class="table" border="1">
		<thead>
		
			<tr valign="top" bgcolor="'.$warnaheader.'">
			<td width="1">&nbsp;</td>
			<td width="1">&nbsp;</td>
			<td width="200"><strong><font color="'.$warnatext.'">PEGAWAI</font></strong></td>
			<td width="200"><strong><font color="'.$warnatext.'">JABATAN</font></strong></td>
			<td width="100"><strong><font color="'.$warnatext.'">VALID AWAL</font></strong></td>
			<td width="100"><strong><font color="'.$warnatext.'">VALID AKHIR</font></strong></td>
			<td width="200"><strong><font color="'.$warnatext.'">KET</font></strong></td>
			</tr>
		
		</thead>
		<tbody>';
		
		
		if ($count != 0)
			{
			do {
				if ($warna_set ==0)
					{
					$warna = $warna01;
					$warna_set = 1;
					}
				else
					{
					$warna = $warna02;
					$warna_set = 0;
					}
		
				$nomer = $nomer + 1;
				$kd = nosql($data['kd']);
				$i_peg_nip = balikin($data['peg_nip']);
				$i_peg_nama = balikin($data['peg_nama']);
				$i_jabatan_nama = balikin($data['jabatan_nama']);
				$i_valid_awal = balikin($data['valid_awal']);
				$i_valid_akhir = balikin($data['valid_akhir']);
				$i_ket = balikin($data['ket']);



			
				
				//pecah tanggal
				$tgl2_pecah = balikin($i_valid_awal);
				$tgl2_pecahku = explode("-", $tgl2_pecah);
				$tgl2_tgl = trim($tgl2_pecahku[2]);
				$tgl2_bln = trim($tgl2_pecahku[1]);
				$tgl2_thn = trim($tgl2_pecahku[0]);
				$i_valid_awal = "$tgl2_tgl/$tgl2_bln/$tgl2_thn";
					
				
				
				//pecah tanggal
				$tgl2_pecah = balikin($i_valid_akhir);
				$tgl2_pecahku = explode("-", $tgl2_pecah);
				$tgl2_tgl = trim($tgl2_pecahku[2]);
				$tgl2_bln = trim($tgl2_pecahku[1]);
				$tgl2_thn = trim($tgl2_pecahku[0]);
				$i_valid_akhir = "$tgl2_tgl/$tgl2_bln/$tgl2_thn";
					
				
	
		
				echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
				echo '<td>
				<input type="checkbox" name="item'.$nomer.'" value="'.$kd.'">
		        </td>
				<td>
				<a href="'.$filenya.'?s=edit&kunci='.$kunci.'&kd='.$kd.'"><img src="'.$sumber.'/img/edit.gif" width="16" height="16" border="0"></a>
				</td>
				<td>
				'.$i_peg_nama.'
				<br>
				NIP.'.$i_peg_nip.'
				</td>
				<td>'.$i_jabatan_nama.'</td>
				<td>'.$i_valid_awal.'</td>
				<td>'.$i_valid_akhir.'</td>
				<td>'.$i_ket.'</td>
		        </tr>';
				}
			while ($data = mysqli_fetch_assoc($result));
			}
		
		echo '</tbody>	
			<tfoot>
		
			<tr valign="top" bgcolor="'.$warnaheader.'">
			<td width="1">&nbsp;</td>
			<td width="1">&nbsp;</td>
			<td width="200"><strong><font color="'.$warnatext.'">PEGAWAI</font></strong></td>
			<td width="200"><strong><font color="'.$warnatext.'">JABATAN</font></strong></td>
			<td width="100"><strong><font color="'.$warnatext.'">VALID AWAL</font></strong></td>
			<td width="100"><strong><font color="'.$warnatext.'">VALID AKHIR</font></strong></td>
			<td width="200"><strong><font color="'.$warnatext.'">KET</font></strong></td>
			</tr>

			</tfoot>
	    
		  </table>

	

		
		
		<table width="100%" border="0" cellspacing="0" cellpadding="3">
		<tr>
		<td>
		<input name="jml" type="hidden" value="'.$count.'">
		<input name="btnALL" type="button" value="SEMUA" onClick="checkAll('.$count.')" class="btn btn-primary">
		<input name="btnBTL" type="reset" value="BATAL" class="btn btn-warning">
		<input name="btnHPS" type="submit" value="HAPUS" class="btn btn-danger">
		
		'.$pagelist.'
		</td>
		</tr>
		</table>';

		
		echo '</div>
		
		</form>
		
	</div>
</div>


<br><br><br>';
?>





<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous" charset="utf-8"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/tokenfield-typeahead.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" charset="utf-8"></script>









<script type="text/javascript">
  $(function() {
  	
  	$.noConflict();



	$('#kunci').typeahead({
      source: function(query, result)
	      {
	      $.ajax({
		      url:"i_cari_pegawai.php",
		      method:"POST",
		      data:{query:query},
		      dataType:"json",
		      success:function(data)
			      {
			      result($.map(data, function(item){
				      return item;
				      }));
			      }
		      })
	      }
      });
     
     
  });


</script>






<?php
//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");



//null-kan
xfree($qbw);
xclose($koneksi);
exit();
?>