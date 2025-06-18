<?php

function generateKode($table, $kolom, $prefix)
{
    $db = \Config\Database::connect();
    $builder = $db->table($table);
    $builder->selectMax($kolom);
    $query = $builder->get();
    $result = $query->getRow();

    if ($result && $result->$kolom) {
        $lastNumber = intval(substr($result->$kolom, strlen($prefix)));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '001';
    }

    return $prefix . $newNumber;
}


function generateIdUser() {
    return generateKode('users', 'id_user', 'U');
}

function generateIdPengajuanDosen() {
    return generateKode('pengajuan_dosen', 'id_pengajuan_dosen', 'PD');
}

function generateIdPengajuanJudul() {
    return generateKode('pengajuan_judul', 'id_pengajuan_judul', 'PJ');
}


?>
