# 🧪 Panduan Test Manual WO Manager

## Server Status
- **Backend:** http://127.0.0.1:8000 ✅ Running
- **Frontend:** http://127.0.0.1:5173 ✅ Running (Vite)

---

## 🔑 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Owner | `owner@womanager.test` | `password` |
| Admin | `admin@womanager.test` | `password` |
| Staff | `staff@womanager.test` | `password` |

---

## 📋 Test Scenarios

### ✅ Scenario 1: Login & Dashboard

**URL:** `http://127.0.0.1:5173/login`

**Steps:**
1. Buka browser, akses URL di atas
2. Login dengan `owner@womanager.test` / `password`
3. **Expected Result:**
   - ✅ Redirect ke dashboard
   - ✅ Tampil statistik (Penghasilan Masuk, Menunggu Pembayaran, dll)
   - ✅ Grafik cashflow muncul (6 bulan terakhir)
   - ✅ List invoice terbaru
   - ✅ List pembayaran terkini

**Check:**
- [ ] Login berhasil
- [ ] Dashboard menampilkan data
- [ ] Grafik muncul
- [ ] No console errors (F12 → Console)

---

### ✅ Scenario 2: Payment - Create (CATAT PEMBAYARAN)

**URL:** `http://127.0.0.1:5173/payments`

**Steps:**
1. Navigasi ke menu **Pembayaran** (sidebar)
2. Klik tombol **"Catat Pembayaran"** (pojok kanan atas)
3. **Expected Result:**
   - ✅ Form pembayaran muncul
   - ✅ Dropdown "Pilih Invoice" menampilkan invoice yang unpaid/partial
   - ✅ Input amount dengan currency formatter (Rp)
   - ✅ Date picker untuk tanggal
   - ✅ Pilihan metode: Tunai, Transfer, QRIS, Lainnya

**Fill Form:**
- **Invoice:** Pilih "INV-20260313-0001 - Putri & Andi"
- **Jumlah:** 5000000 (atau ketik "5000000", auto-format)
- **Tanggal:** Hari ini
- **Metode:** Transfer (klik)
- **Catatan:** "Pembayaran via transfer BCA"

**Submit:**
4. Klik **"Catat Pembayaran"**
5. **Expected Result:**
   - ✅ Data tersimpan
   - ✅ Redirect ke list payments
   - ✅ Payment baru muncul di list
   - ✅ Invoice status ter-update (remaining amount berkurang)

**Check:**
- [ ] Form tampil dengan benar
- [ ] Dropdown invoice menampilkan data
- [ ] Currency input berfungsi (auto-format Rp)
- [ ] Submit berhasil
- [ ] Redirect ke list
- [ ] Payment baru muncul

---

### ✅ Scenario 3: Payment - Edit

**URL:** `http://127.0.0.1:5173/payments`

**Steps:**
1. Di list payments, cari payment yang baru dibuat
2. Klik icon **Edit** (pensil)
3. **Expected Result:**
   - ✅ Form edit muncul dengan data ter-load
   - ✅ Invoice dropdown disabled (tidak bisa diubah)
   - ✅ Amount, tanggal, method, notes terisi

**Edit Data:**
4. Ubah **Jumlah** menjadi: 7000000
5. Ubah **Catatan:** "Revisi - pembayaran via Mandiri"
6. Klik **"Simpan Perubahan"**
7. **Expected Result:**
   - ✅ Data ter-update
   - ✅ Redirect ke list
   - ✅ Invoice remaining amount ter-update otomatis

**Check:**
- [ ] Edit form muncul
- [ ] Data ter-load dengan benar
- [ ] Invoice dropdown disabled
- [ ] Update berhasil
- [ ] Invoice recalculate otomatis

---

### ✅ Scenario 4: Payment - Delete

**URL:** `http://127.0.0.1:5173/payments`

**Steps:**
1. Di list payments, klik icon **Delete** (trash) pada payment
2. **Expected Result:**
   - ✅ Modal konfirmasi muncul
   - ✅ Menampilkan amount dan invoice number

**Confirm:**
3. Klik **"Hapus"**
4. **Expected Result:**
   - ✅ Payment terhapus dari list
   - ✅ Invoice status kembali (remaining amount bertambah)

**Check:**
- [ ] Modal konfirmasi muncul
- [ ] Delete berhasil
- [ ] Invoice recalculate

---

### ✅ Scenario 5: Invoice - Create & Auto-number

**URL:** `http://127.0.0.1:5173/invoices/create`

**Steps:**
1. Navigasi ke menu **Invoice**
2. Klik **"Terbitkan Invoice"**
3. **Expected Result:**
   - ✅ Form invoice muncul
   - ✅ Invoice number auto-generated (INV-YYYYMMDD-000X)

**Fill Form:**
- **Client:** Pilih "Putri & Andi"
- **Tanggal Invoice:** Hari ini
- **Jatuh Tempo:** +14 hari
- **DP:** 10000000
- **Items:** 
  - Description: "Wedding Decoration"
  - Qty: 1
  - Price: 15000000
- **Notes:** "Test invoice"

**Submit:**
4. Klik **"Terbitkan Invoice"**
5. **Expected Result:**
   - ✅ Invoice tersimpan
   - ✅ Invoice number format: INV-20260313-0002
   - ✅ Total: Rp 15.000.000
   - ✅ DP: Rp 10.000.000
   - ✅ Remaining: Rp 5.000.000
   - ✅ Status: partial

**Check:**
- [ ] Invoice number auto-generated
- [ ] Format benar (INV-YYYYMMDD-XXXX)
- [ ] Total calculate otomatis
- [ ] Status correct (unpaid/partial/paid)

---

### ✅ Scenario 6: Dashboard - Cashflow Chart

**URL:** `http://127.0.0.1:5173/`

**Steps:**
1. Kembali ke Dashboard
2. **Expected Result:**
   - ✅ Grafik line chart muncul
   - ✅ Data 6 bulan terakhir
   - ✅ Tooltip menampilkan amount saat hover
   - ✅ Gradient fill di bawah line

**Check:**
- [ ] Chart render
- [ ] Data muncul (tidak kosong)
- [ ] Hover tooltip berfungsi
- [ ] No console errors

---

### ✅ Scenario 7: Dashboard - Stat Cards

**URL:** `http://127.0.0.1:5173/`

**Expected Display:**
- **Penghasilan Masuk:** Total pembayaran bulan ini (Rp)
- **Menunggu Pembayaran:** Total invoice belum lunas (Rp)
- **Invoice Belum Lunas:** Count invoice unpaid/partial
- **Clients Baru (Bulan Ini):** Count client baru

**Check:**
- [ ] 4 stat cards muncul
- [ ] Angka tampil (tidak 0 semua)
- [ ] Icon sesuai
- [ ] Color coding benar (success/warning/danger/accent)

---

## 🐛 Bug Checklist

Jika menemukan issue, check:

### Console Errors
- Buka DevTools (F12)
- Tab **Console**
- Screenshot error jika ada

### Network Errors
- Tab **Network**
- Refresh page
- Check request berwarna merah (failed)
- Lihat response error

### UI Issues
- Form tidak submit? Check validation messages
- Data tidak muncul? Check loading state
- Redirect error? Check router console

---

## 📊 Test Results Template

Copy-paste ini dan centang yang sudah ditest:

```
### Test Results - [DATE]

**Tester:** [Your Name]

#### Login & Dashboard
- [ ] Login berhasil
- [ ] Dashboard tampil
- [ ] Grafik muncul
- [ ] Stat cards correct

#### Payment Create
- [ ] Form tampil
- [ ] Dropdown invoice working
- [ ] Currency input working
- [ ] Submit success
- [ ] Invoice recalculate

#### Payment Edit
- [ ] Edit form load data
- [ ] Update success
- [ ] Invoice recalculate

#### Payment Delete
- [ ] Delete modal
- [ ] Delete success
- [ ] Invoice recalculate

#### Invoice Create
- [ ] Auto-number working
- [ ] Calculate total
- [ ] Status correct

#### Issues Found:
1. [Description]
2. [Description]

#### Screenshot:
- [Attach if needed]
```

---

## 🚀 Quick Test Commands

### Check Backend Running
```bash
curl http://127.0.0.1:8000/api/user -H "Accept: application/json"
# Should return: {"message":"Unauthenticated."} or user data
```

### Check Frontend Running
```bash
curl http://127.0.0.1:5173
# Should return HTML
```

### Reset Database
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=TestDataSeeder
```

---

## ✅ Expected Behavior Summary

| Feature | Expected | Status |
|---------|----------|--------|
| Login | Success, redirect to dashboard | ✅ |
| Dashboard | Stats + Chart visible | ✅ |
| Payment List | Show all payments | ✅ |
| Payment Create | Form + save + recalculate | ✅ |
| Payment Edit | Load + update + recalculate | ✅ |
| Payment Delete | Confirm + delete + recalculate | ✅ |
| Invoice Create | Auto-number + calculate | ✅ |
| Routes | All 15+ API routes available | ✅ |

---

**Happy Testing! 🎉**
