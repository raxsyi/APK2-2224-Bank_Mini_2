<?php
// Set Waktu
date_default_timezone_set('Asia/Jakarta');
$tgl = date('Y-m-d H:i:s');

// Koneksi Database
$HOSTNAME = "localhost";
$DATABASE = "db_apk_bankmini";
$USERNAME = "root";
$PASSWORD = "";


$KONEKSI = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$KONEKSI) {

    die("ERROR BANG!!... BACA TUH EROR -->" . mysqli_connect_error($KONEKSI));
}

if (!function_exists('autonumber')) {
    function autonumber($tabel, $kolom, $lebar = 0, $awalan) {
        global $KONEKSI;
        $auto = mysqli_query($KONEKSI, "SELECT $kolom FROM $tabel ORDER BY $kolom DESC LIMIT 1") or die(mysqli_error($KONEKSI));
        $jumlah_record = mysqli_num_rows($auto);
        
        if ($jumlah_record == 0) {
            $nomor = 1;
        } else {
            $row = mysqli_fetch_array($auto);
            $nomor = intval(substr($row[0], strlen($awalan))) + 1;
        }

        if ($lebar > 0) {
            $angka = $awalan . str_pad($nomor, $lebar, "0", STR_PAD_LEFT);
        } else {
            $angka = $awalan . $nomor;
        }
        
        return $angka;
    }
}

    echo autonumber("tbl_users","id_user",3,"USR");

// Fungsi Register
if (!function_exists('registrasi')){
function registrasi($DATA)
{
    global $KONEKSI;
    global $tgl;

    $nama = stripslashes($DATA["nama"]); // untuk cek fOrm register dari input nama
    $email = strtolower(stripslashes($DATA["email"])); // memastikan fOrm register mengisi input email berupa huruf kecil
    $id_user = stripslashes($DATA["id_user"]);
    $password = mysqli_real_escape_string($KONEKSI, $DATA["password"]);
    $password2 = mysqli_real_escape_string($KONEKSI, $DATA["password2"]);


    //echo $nama . "|" . $email . "|" . $password . "|" . $password2;

    // cek email yang diinput sudah ada?
    $result = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email='$email'");

    if (mysqli_fetch_assoc($result)) {
        echo '<script>
                alert("email sudah digunakan");
            </script>';
        return false;
    }

    // cek pasword
    if ($password !== $password2) {
        echo '<script>
                alert("Password tidak sesuai");
                document.location.href="register.php";
            </script>';
        return false;
    }

    // encrypt password ke db
    $password_crypt = password_hash($password, PASSWORD_DEFAULT); // pakai algorithm default hash

    // ambil id tipe user di tbl_tipe_user
    $tipe_user = "SELECT * FROM tbl_tipe_user WHERE tipe_user = 'Admin'";
    $hasil = mysqli_query($KONEKSI, $tipe_user);
    $row = mysqli_fetch_assoc($hasil);
    $id_role = $row['id_tipe_user'];



    //tambah user baru ke tbl_users
    $SQL_USER = "INSERT INTO tbl_users SET
    id_user = '$id_user',
    email = '$email',
    role = '$id_role',
    password = '$password_crypt',
    create_at = '$tgl'";

    mysqli_query($KONEKSI, $SQL_USER) or die("gagal menambah user -->" . mysqli_error($KONEKSI));

    //tambah user baru ke tbl_admin
    $SQL_ADMIN = "INSERT INTO tbl_admin SET
    id_user = '$id_user',
    nama_admin = '$nama',
    create_at = '$tgl'";

    mysqli_query($KONEKSI, $SQL_ADMIN) or die("gagal menambah user -->" . mysqli_error($KONEKSI));

    echo '<script>
            document.location.href="login.php"
        </script>';

    return mysqli_affected_rows($KONEKSI);
}
}

// FUngsi Tampil data
if (!function_exists('tampil')){
function tampil($DATA)
{
    global $KONEKSI;

    $hasil = mysqli_query($KONEKSI, $DATA);
    $rows = []; // siapkan variable/wadah kosong untuk data dari db

    while ($row = mysqli_fetch_assoc($hasil)) {
        $rows[] = $row; // dimasukkan datanya disini    
    }
    return $rows;
}
}

?>