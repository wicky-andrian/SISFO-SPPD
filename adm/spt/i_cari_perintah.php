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

//ambil nilai
require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");

nocache;

//nilai
$judul = "cari nama";
$juduli = $judul;



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//nilai
$searchTerm = cegah($_GET['query']);


$query = "SELECT * FROM m_pemberi_perintah ".
			"WHERE peg_nip LIKE '%".$searchTerm."%' ".
			"OR peg_nama LIKE '%".$searchTerm."%' ".
			"ORDER BY peg_nama ASC LIMIT 0,5";
$result = mysqli_query($koneksi, $query);

$data = array();


if (mysqli_num_rows($result) > 0)
    {
    while ($row = mysqli_fetch_assoc($result))
	    {
	    $i_nip = balikin($row["peg_nip"]);
	    $i_nama = balikin($row["peg_nama"]);
	    $data[] = "$i_nama NIP.$i_nip";
	    }

    echo json_encode($data);

	}




exit();
?>