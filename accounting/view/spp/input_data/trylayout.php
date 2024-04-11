<?php 
    
    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }
 
    function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }           
        return $hasil;
    }

    $angka = 3999999 . " Rupiah";
    echo ucwords(terbilang($angka));

    $code_accounting = $_SESSION['c_accounting'];

    $sqlJuz = mysqli_query($con,
    "select CONCAT('Juz ', tblall.juz_atau_keterangan_ayat,' Surah ', nmbagian) as nmjuzall, tblall.* from 
    (
        select tbl1.* from
        (
            select tj.id, tbl1.juz_atau_keterangan_ayat, tj.juz_atau_keterangan_ayat as nmbagian, count(sh.c_siswa) as jml, tj.seqjuz from tbl_juz tj
            left join sisjuz_h sh on tj.id = sh.idjuz 
            left join (select distinct tj2.id, tj2.juz_atau_keterangan_ayat from tbl_juz tj2 ) as tbl1 
            on tj.parentid  = tbl1.id
            where tj.parentid != 0
            
            and coalesce(sh.flag, 'N') = 'N'
            group by tj.id, tj.juz_atau_keterangan_ayat, tbl1.juz_atau_keterangan_ayat, tj.seqjuz
            order by tj.seqjuz
        ) as tbl1
    union 
        select tbl2.* from
        ( 
            select tj.id, '' juz_atau_keterangan_ayat, tj.juz_atau_keterangan_ayat as nmbagian, count(sh.c_siswa) as jml, tj.seqjuz  from tbl_juz tj
            left join sisjuz_h sh on tj.id = sh.idjuz  
            where tj.parentid = 0 and tj.seqjuz  > 14
            and coalesce(sh.flag, 'N') = 'N'
            group by tj.id, tj.juz_atau_keterangan_ayat, tj.seqjuz
            order by tj.seqjuz
        ) as tbl2
   ) as tblall
   order by seqjuz"); 

    $queryGetDataSeqJuz1 = mysqli_query($con, 
        "SELECT id, juz_atau_keterangan_ayat, seqjuz FROM tbl_juz WHERE seqjuz = '1' "
    );

    $getDataArr = mysqli_fetch_array($queryGetDataSeqJuz1);
    
    $getDataIdJuz  = $getDataArr['id'];
    $getDataSeqJuz = $getDataArr['seqjuz'];

    $dataBulan = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $pwBuatBuDian = password_hash('dian', PASSWORD_DEFAULT);
    $pwBuatBuRani = password_hash('rani', PASSWORD_DEFAULT);

    // echo "Bu Dian : " . $pwBuatBuDian . "<br>" . "Bu Rani : " . $pwBuatBuRani;exit;

    $sqlGetUser         = "SELECT * FROM accounting WHERE username = 'rani' ";
    $execQueryGetUser   = mysqli_query($con, $sqlGetUser);

    $getData = mysqli_fetch_array($execQueryGetUser);

    $dataPassword = $getData['password'];

?>

<div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12">

        <?php if(isset($_SESSION['pesan']) && $_SESSION['pesan'] == 'tambah'){?>
          <div style="display: none;" class="alert alert-warning alert-dismissable">Setup Naik Juz Berhasil Disimpan
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             <?php unset($_SESSION['pesan']); ?>
          </div>
        <?php } ?>

        <?php if(isset($_SESSION['pesan']) && $_SESSION['pesan'] == 'edit'){?>
          <div style="display: none;" class="alert alert-info alert-dismissable">Data Naik Juz Berhasil Disimpan
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             <?php unset($_SESSION['pesan']); ?>
          </div>
        <?php } ?>

        <?php if(isset($_SESSION['pesan']) && $_SESSION['pesan'] == 'edit_catatan') { ?>
          <div style="display: none;" class="alert alert-success alert-dismissable"> Histori Catatan Berhasil Disimpan
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             <?php unset($_SESSION['pesan']); ?>
          </div>
        <?php } ?>

        <?php if(isset($_SESSION['pesan']) && $_SESSION['pesan'] == 'hapus') {?>
          <div style="display: none;" class="alert alert-info alert-dismissable">Data Naik Juz Berhasil Dihapus
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
             <?php unset($_SESSION['pesan']); ?>
          </div>
        <?php } ?>

        <?php if(isset($_SESSION['err_warning']) && $_SESSION['err_warning'] == 'err_validation'){?>
          <div style="display: none;" class="alert alert-danger alert-dismissable"> Mohon Cari Siswa Terlebih Dahulu
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php unset($_SESSION['err_warning']); ?>
          </div>
        <?php } ?>

    </div>
</div>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"> <i class="glyphicon glyphicon-new-window"></i> Data Pembayaran </h3><span style="float:right;"><a class="btn btn-primary" onclick="OpenCarisiswaModal()"><i class="glyphicon glyphicon-plus"></i> Cari Siswa</a></span>
       
    </div>
    <form action="<?php echo $basegu; ?>a-guru/<?php echo md5('addnaikjuz'); ?>/access" method="post">
        <div class="box-body table-responsive">
            <input type="hidden" id="_entryby" name="_entryby" class="form-control" value="<?php echo $na['nama'] ?>">
            <input type="hidden" id="_idsiswa" name="_idsiswa" class="form-control">
            <input type="hidden" id="_idjuz" name="_idjuz" class="form-control">
            <input type="hidden" id="_idjuznext" name="_idjuznext" class="form-control">
            <input type="hidden" id="_nmjuznext" name="_nmjuznext" class="form-control">
            <input type="hidden" id="_seqnext" name="_seqnext" class="form-control">
            <input type="hidden" id="code_siswa" name="code_siswa" class="form-control">

            <input type="hidden" class="form-control" id="_nmsiswa" name="_nmsiswa">
            <input type="hidden" class="form-control" id="_juzcur" name="_juzcur"/>
            <input type="hidden" class="form-control" id="_juzutama" name="_juzutama"/>

            <input type="hidden" id="_idjuzmanual" name="_idjuzmanual" class="form-control">
            <input type="hidden" id="_seqnextmanual" name="_seqnextmanual" class="form-control">
            <input type="hidden" id="_nmjuzmanual" name="_nmjuzmanual" class="form-control">
            <input type="hidden" id="_nmbagianmanual" name="_nmbagianmanual" class="form-control">

            <div class="row">

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" class="form-control" value="127" name="_bagianjuzcur2" id="_bagianjuzcur2" readonly="">
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>NIS</label>
                        <input type="text" class="form-control" value="202202127" name="_bagianjuzcur2" id="_bagianjuzcur2" readonly="">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text" name="" class="form-control" value="MUHAMMAD ELVARO RAFARDHAN" id="_nmsiswa2" name="_nmsiswa2" />
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Kelas</label>
                        <input type="text" name="" value="2 SD" class="form-control" value="MUHAMMAD ELVARO RAFARDHAN" id="_nmsiswa2" name="_nmsiswa2" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Panggilan</label>
                        <input type="text" class="form-control" id="_kelassiswa" value="RAFARDHAN" name="_kelassiswa" />
                    </div>
                </div>
            </div> 

            <hr class="new1" />

            <table id="example1" class="table table-bordered table-hover">
                <thead>
                  <tr>
                     <th width="5%">NO</th>
                     <th style="text-align: center;">Nama</th>
                     <th style="text-align: center;">Juz</th>
                     <th style="text-align: center;">Bagian</th>
                     <th style="text-align: center;">Tgl Naik</th>
                     <th style="text-align: center;"> Jml Hari </th>
                  </tr>
                </thead>
                <tbody>

                    <tr>
                        <td style="text-align: center;"> 1 </td>
                        <td style="text-align: center;"><a style="cursor:pointer;"> NISWA </a> </td>
                        <td style="text-align: center;"> lorem </td>
                        <td style="text-align: center;"> ipsum </td>
                        <td style="text-align: center;"> test </td>
                        <td style="text-align: center;"> lorem ipsum </td>
                    </tr>

                    <tr>
                        <td style="text-align: center;"> 2 </td>
                        <td style="text-align: center;"><a style="cursor:pointer;"> GATHAN </a> </td>
                        <td style="text-align: center;"> Bekasi </td>
                        <td style="text-align: center;"> 16 Desember 2002 </td>
                        <td style="text-align: center;"> Trisakti </td>
                        <td style="text-align: center;"> English </td>
                    </tr>

                </tbody>

            </table>

            <?php  

                $no = 1;

                if ($code_accounting == 'accounting1') {
                    
                    // histori input data sd
                    $queryGetAllDataHistori     = "SELECT * FROM input_data_sd limit 10";
                    $execQueryGetAllDataHistori = mysqli_query($con, $queryGetAllDataHistori);

                } else {

                    // histori input data tk
                    $queryGetAllDataHistori     = "SELECT * FROM input_data_tk";
                    $execQueryGetAllDataHistori = mysqli_query($con, $queryGetAllDataHistori);

                }

            ?>

            <?php if ($code_accounting == 'accounting1'): ?>
                
                    <!-- <table id="example1" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                             <th style="text-align: center;"> IDw </th>
                             <th style="text-align: center;"> NIS </th>
                             <th style="text-align: center;"> DATE </th>
                             <th style="text-align: center;"> BULAN </th>
                             <th style="text-align: center;"> KELAS </th>
                             <th style="text-align: center;"> NAMA KELAS</th>
                             <th style="text-align: center;"> NAMA </th>
                             <th style="text-align: center;"> PANGGILAN </th>
                             <th style="text-align: center;"> TRANSAKSI </th>
                             <th style="text-align: center;"> SPP SET </th>
                             <th style="text-align: center;"> PANGKAL SET </th>
                             <th style="text-align: center;"> SPP  </th>
                             <th style="text-align: center;"> KET SPP </th>
                             <th style="text-align: center;"> KEGIATAN </th>
                             <th style="text-align: center;"> KET KEGIATAN </th>
                             <th style="text-align: center;"> BUKU </th>
                             <th style="text-align: center;"> KET BUKU </th>
                             <th style="text-align: center;"> SERAGAM </th>
                             <th style="text-align: center;"> KET SERAGAM </th>
                             <th style="text-align: center;"> REGISTRASI </th>
                             <th style="text-align: center;"> KET REGISTRASI </th>
                             <th style="text-align: center;"> LAIN </th>
                             <th style="text-align: center;"> KET LAIN </th>
                             <th style="text-align: center;"> DI INPUT OLEH </th>
                             <th style="text-align: center;"> STAMP </th>
                          </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($execQueryGetAllDataHistori as $data_histori) : ?>
                                <tr>
                                    <td style="text-align: center;"> <?= $data_histori['ID']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['NIS']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['DATE']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BULAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KELAS']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['NAMA_KELAS']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['NAMA']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGGILAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['TRANSAKSI']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP_SET']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL_SET']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KEGIATAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KEGIATAN_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BUKU']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BUKU_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SERAGAM']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SERAGAM_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['REGISTRASI']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['REGISTRASI_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['LAIN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['LAIN_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['INPUTER']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['STAMP']; ?> </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>

                    </table> -->

            <?php else: ?>

                <!-- <div style="overflow-x:auto;">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                             <th style="text-align: center;"> ID </th>
                             <th style="text-align: center;"> NIS </th>
                             <th style="text-align: center;"> DATE </th>
                             <th style="text-align: center;"> BULAN </th>
                             <th style="text-align: center;"> KELAS </th>
                             <th style="text-align: center;"> NAMA </th>
                             <th style="text-align: center;"> PANGGILAN </th>
                             <th style="text-align: center;"> TRANSAKSI </th>
                             <th style="text-align: center;"> SPP SET </th>
                             <th style="text-align: center;"> PANGKAL SET </th>
                             <th style="text-align: center;"> SPP  </th>
                             <th style="text-align: center;"> KET SPP </th>
                             <th style="text-align: center;"> KEGIATAN </th>
                             <th style="text-align: center;"> KET KEGIATAN </th>
                             <th style="text-align: center;"> BUKU </th>
                             <th style="text-align: center;"> KET BUKU </th>
                             <th style="text-align: center;"> SERAGAM </th>
                             <th style="text-align: center;"> KET SERAGAM </th>
                             <th style="text-align: center;"> REGISTRASI </th>
                             <th style="text-align: center;"> KET REGISTRASI </th>
                             <th style="text-align: center;"> LAIN </th>
                             <th style="text-align: center;"> KET LAIN </th>
                             <th style="text-align: center;"> DI INPUT OLEH </th>
                             <th style="text-align: center;"> STAMP </th>
                          </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($execQueryGetAllDataHistori as $data_histori) : ?>
                                <tr>
                                    <td style="text-align: center;"> <?= $data_histori['ID']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['NIS']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['DATE']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BULAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KELAS']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['NAMA']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGGILAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['TRANSAKSI']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP_SET']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL_SET']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SPP_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['PANGKAL_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KEGIATAN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['KEGIATAN_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BUKU']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['BUKU_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SERAGAM']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['SERAGAM_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['REGISTRASI']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['REGISTRASI_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['LAIN']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['LAIN_txt']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['INPUTER']; ?> </td>
                                    <td style="text-align: center;"> <?= $data_histori['STAMP']; ?> </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>

                    </table>
                </div> -->

            <?php endif ?>

        </div>
    </form>
    
</div>

<!-- Modal Cari Siswa -->
<div id="datamassiswa" class="modal"  data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel"> <i class="glyphicon glyphicon-calendar"></i> Data Siswa</h4>
            </div>
            <div class="modal-body"> 
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                              <th style="text-align: center;" width="5%">NO</th>
                            <?php 
                                if(empty($_GET['q'])) {
                                    echo '<th style="text-align: center;" width="12%">KELAS</th>';
                                } 
                            ?>
                              <th style="text-align: center;">NIS</th>
                              <th style="text-align: center;">NAMA</th>
                              <th style="text-align: center;">GENDER</th>
                            </tr>
                        </thead>
                        <?php

                            $no = 1;
                            
                            if(isset($_GET['q'])) {
                              $smk=mysqli_query($con,"SELECT * FROM siswa where c_kelas='$_GET[q]' order by nama asc ");
                            } else {

                                if ($code_accounting == 'accounting1') {
                                    $queryGetAllDataSiswa      = "SELECT * FROM data_murid_sd ORDER BY KELAS asc ";
                                    $execqueryGetAllDataSiswa  = mysqli_query($con, $queryGetAllDataSiswa);
                                } else {
                                    $queryGetAllDataSiswa      = "SELECT * FROM data_murid_tk";
                                    $execqueryGetAllDataSiswa  = mysqli_query($con, $queryGetAllDataSiswa);
                                }

                            } 

                        ?>
                        <tbody>
                            <?php foreach ($execqueryGetAllDataSiswa as $data): ?>
                            <tr>
                                <td style="text-align: center;"> <?= $no++; ?> </td>
                                <td style="text-align: center;"> <?= $data['KELAS']; ?> </td>
                                <td style="text-align: center;"> <?= $data['NIS']; ?> </td>
                                <td style="text-align: center;"> <?= $data['Nama']; ?> </td>
                                <?php if ($data['jk'] == 'L'): ?>
                                    <td style="text-align: center;"> Laki - Laki </td>
                                <?php else: ?>
                                    <td style="text-align: center;"> Perempuan </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>    
</div>
<!-- Akhir Modal Cari Siswa -->

<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script> -->

<script language="javascript" type="text/javascript">

$(document).ready(function() {

    $('#_idsiswa').val("");
    $('#_nmsiswa').val("");
    $('#_nmsiswa2').text("");
    $('#_kelassiswa').text("");

    $('#editorcatatan').summernote({
        placeholder: 'Isi Catatan',
        tabsize: 2,
        height: 150
      });

    var _btnsetupjuz = document.getElementById("btnsetupjuz");
    _btnsetupjuz.style.display = "none";

    // $("#btnSimpanCatatan").click(function() {
    //     alert("Hello")
    //     $.ajax({
    //         url     : "../../a-guru/control.php",
    //         type    : "POST",
    //         data    : {
    //             datanama : document.getElementById("_nmsiswa2").value
    //         },
    //         success:function(data) {
    //             console.log(data);
    //         }
    //     });
    // })

});

    function OpenCarisiswaModal(){

        $('#datamassiswa').modal("show");
    }

    function OnSiswaSelectedModal(kode, nama, kelas, c_siswa, curjuz, idcurjuz, seqjuz, 
    nextSeq, nextnmjuz, nextidjuz, nmbagian, catatan, nextjuzutama){

        // alert(kode)

        $('#_idsiswa').val(kode);
        $('#_nmsiswa').val(nama);
        $('#_nmsiswa2').text(nama);
        $('#_kelassiswa').text(kelas);

        //$('#editorcatatan').summernote('reset');
        $('#editorcatatan').summernote('code', '<p><br></p>');

        // Jika code siswa tidak ada di tabel sisjuz_h
        if(c_siswa == undefined || c_siswa == null || c_siswa == '') {

            let btnnaikjuz              = document.getElementById("btnnaikjuz");
            btnnaikjuz.style.display    = "block";
            btnnaikjuz.style.marginTop  = "10px";

            alert(`${nama} belum ada di data naik juz`);
            document.getElementById('code_siswa').value = kode

            document.getElementById("isijuzsekarang").value = 30
            document.getElementById("_setmanualjuzselect").value = "kosong"
            $('#_juzcur').val("An Nas - Al Fajr");
            $('#_bagianjuzcur2').val("An Nas - Al Fajr");
            $('#_idjuz').val(`<?= $getDataIdJuz; ?>`);
            $('#_seqnext').val(`<?= $getDataSeqJuz; ?>`);
            
        } else {

            // Jika code siswa ada di table sisjuz_h tetapi di kolom juz dan kolom ketjuzsurah tidak ada data nya
            let btnnaikjuz              = document.getElementById("btnnaikjuz");
            btnnaikjuz.style.display    = "block";
            btnnaikjuz.style.marginTop  = "10px";

            $('#_idjuz').val(nextidjuz);
            $('#_seqnext').val(nextSeq);
            $('#_bagianjuzcur2').val(nmbagian ?? "");
            $('#_nmjuznext').val(nextnmjuz ?? "");
            
            $('#_juzutama').val(nextjuzutama ?? "");
            
            fetch("view/tahfidz/singledatajuz_h.php?c_siswa=" + c_siswa)
            .then((response) => {
                if(!response.ok){ // Before parsing (i.e. decoding) the JSON data,// check for any errors.// In case of an error, throw.
                    throw new Error("Terjadi kesalahan!");
                }

                return response.json(); // Parse the JSON data.
            })
            .then((data) => {
                // console.log(data.catatan)
                // alert('fetch');
                const myJSON                = JSON.stringify(data.catatan);
                const ketjuzsurahsekarang   = JSON.stringify(data.juz);
                const idjuzsekarang         = JSON.stringify(data.idjuz);
                document.getElementById("isijuzsekarang").value = ketjuzsurahsekarang.slice(1, -1).trim()
                // alert(ketjuzsurahsekarang.slice(1, -1).trim())

                // This is where you handle what to do with the response.
                $('#editorcatatan').summernote('pasteHTML', myJSON.slice(1, -1).trim());
                $('#_juzcur').val(ketjuzsurahsekarang.slice(1, -1).trim());

                if(idjuzsekarang.slice(1, -1).trim() == "23")
                {
                    _btnnaikjuz.style.display = "none";
                }

            })
            .catch((error) => {
                // This is where you handle errors.
            });

        }

        $('#datamassiswa').modal("hide");
    }

    function SelectilidChanged(juzval) {

        if($('#_idsiswa').val() == null || $('#_idsiswa').val() == "")
        {
            alert("Silahkan pilih siswa");
            return $('#_setmanualjuzselect').val("");
        }

        fetch("view/tahfidz/apiservicemasterjuz.php?idjuz=" + $('#_setmanualjuzselect').val())
            .then((response) => {
                if(!response.ok){ // Before parsing (i.e. decoding) the JSON data,// check for any errors.// In case of an error, throw.
                    throw new Error("Terjadi kesalahan!");
                }

                return response.json(); // Parse the JSON data.
            })
            .then((data) => {
                var valjuz = JSON.stringify(data.nmjuz2);
                var valnmbagian = JSON.stringify(data.nmbagian);
                var valseq = JSON.stringify(data.seqjuz);

                $('#_idjuzmanual').val($('#_setmanualjuzselect').val());
                $('#_seqnextmanual').val(valseq.slice(1, -1).trim());
                $('#_nmjuzmanual').val(valjuz.slice(1, -1).trim());
                $('#_nmbagianmanual').val(valnmbagian.slice(1, -1).trim());


                //alert("id: " + $('#_setmanualjuzselect').val() + "- jilid: " + valjuz + "- bagian: " + valnmbagian);
            })
            .catch((error) => {
                // This is where you handle errors.
            });

    } 

</script>

<!-- jQuery 2.2.3 -->

<script type="text/javascript" src="<?php echo $base; ?>theme/datetime/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo $base; ?>theme/datetime/js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
        showMeridian: 1
    });
  $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0
    });
  $('.form_time').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 1,
    minView: 0,
    maxView: 1,
    forceParse: 0
    });
</script>




