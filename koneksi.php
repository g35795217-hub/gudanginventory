<?php
// ============================================================
// KONEKSI KE GOOGLE SHEETS (spreadsheet "inventory") via Google Apps Script
// Isi dua nilai di bawah ini. File ini hanya dibaca server, tidak dikirim ke browser.
// ============================================================
const APPS_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbyOOKe7pT001Fsfv_zgigjuq5hEvarGqqmu7OQjWlyQiYLRX2zM-KUy5vKSwEj7S4Dh6Q/exec';   // URL "Web app" hasil Deploy (berakhiran /exec)
const API_KEY         = 'pln2026'; // harus sama dengan Script Property API_KEY

// Tanggal stok awal: tanggal Kartu gantung (SAP) yang dipakai sebagai Stok Awal di Data Material.
// Transaksi bertanggal SAMA ATAU SEBELUM tanggal ini dianggap riwayat (sudah tercermin di stok awal) dan tidak mengubah stok;
// transaksi SESUDAH tanggal ini menambah / mengurangi stok. Ubah bila Kartu gantung Anda diambil pada tanggal lain.
const STOK_AWAL_TANGGAL = '2026-11-05';

date_default_timezone_set('Asia/Jakarta');
session_start();
require __DIR__ . '/fungsi.php';
