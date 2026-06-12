CHECKLIST TEST MANUAL - KEUANGAN OPERASIONAL

Instruksi singkat:
Lakukan test berikut di lingkungan staging atau development. Jangan jalankan pada production tanpa persetujuan dan backup.

1. Buat Penerimaan ZIS kategori allow_operational_transfer = true (mis. "Infak Jumat").
   - Pastikan Total Kas Masuk Operasional pada halaman Keuangan Operasional naik sesuai nominal penerimaan.

2. Buat Penerimaan ZIS kategori allow_operational_transfer = false (mis. "Zakat Maal").
   - Pastikan Total Kas Masuk Operasional TIDAK naik dari penerimaan kategori ini.

3. Buat Pengeluaran Operasional (Tambah Pengeluaran) dengan nominal <= saldo operasional.
   - Pastikan transaksi berhasil disimpan dan Total Kas Keluar meningkat sesuai.
   - Pastikan Saldo berkurang sesuai nominal.

4. Buat Pengeluaran Operasional melebihi saldo operasional.
   - Pastikan sistem menolak dengan pesan validasi bahwa saldo tidak mencukupi.

5. Cek transaksi lama dengan source_type = 'zis_distribution'.
   - Pastikan transaksi tersebut tetap tampil di daftar transaksi (history).
   - Pastikan total masuk operasional tidak double-count karena adanya transaksi transfer-from-zis.

6. General checks:
   - Pastikan daftar transaksi tampil normal (pagination, filter, detail link).
   - Pastikan tombol: Kategori, Akun Kas, Mutasi, dan Tambah Pengeluaran berfungsi.

Catatan untuk tester:
- Jika menemukan perbedaan perhitungan dengan laporan lama, catat contoh transaksi (ID, tanggal, nominal, source_type) untuk analisa.
- Jangan menghapus atau memodifikasi transaksi existing selama pengujian.

Tanggal: 2026-06-12
Dibuat oleh: Copilot
